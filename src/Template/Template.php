<?php

  namespace ReRoute\Template;


  /**
   * @package ReRoute\Template
   */
  class Template {

    /**
     * @var string
     */
    protected $templateMatch = null;

    /**
     * @var string
     */
    protected $templateBuild = null;

    /**
     * @var array
     */
    protected $parameters = [];


    /**
     * @param $template
     */
    public function __construct($template) {

      if (!is_string($template)) {
        throw new \InvalidArgumentException("Invalid template. Expect string. Given: " . gettype($template));
      }

      $this->prepare($template);
    }


    /**
     * @param string $template
     */
    protected function prepare($template) {

      $this->templateMatch = $template;
      $this->templateBuild = $template;

      preg_match_all('![\{\}]!', $template, $delimiters, PREG_OFFSET_CAPTURE);


      $delimiters = $delimiters[0];
      if (empty($delimiters)) {
        return;
      }
      # 1. skip quoted delimiters
      foreach ($delimiters as $key => $rawParameterInfo) {
        $symbol = mb_substr($template, $rawParameterInfo[1] - 1, 1);
        if ($symbol == '\\') {
          unset($delimiters[$key]);
        }
      }

      $delimiters = array_values($delimiters);


      if (count($delimiters) % 2 !== 0) {
        throw new \InvalidArgumentException("Invalid template. Different number of delimiters");
      }


      $parameters = [];
      $previousStartIndex = null;
      $state = null;

      foreach ($delimiters as $rawParameterInfo) {
        if ($state == null) {
          $state = 1;
          $previousStartIndex = $rawParameterInfo[1];
          continue;
        }

        if ($rawParameterInfo[0] == '}') {
          $state--;
        } else {
          $state++;
        }


        if ($state == 0 and $previousStartIndex !== null) {
          $parameters[] = mb_substr($template, $previousStartIndex, $rawParameterInfo[1] - $previousStartIndex + 1);
          $previousStartIndex = null;
        }

      }


      # rebuild groups with names and value params
      foreach ($parameters as $i => $rawParameterInfo) {
        unset($parameters[$i]);

        preg_match('!^\{([a-z][0-9a-z]*)(:|\})!i', $rawParameterInfo, $rawGroupName);


        if (empty($rawGroupName[1])) {
          throw new \InvalidArgumentException("Cant detect parameter name");
        }

        $name = $rawGroupName[1];

        if (isset($parameters[$name])) {
          throw new \InvalidArgumentException("Parameter with name already defined:" . $name);
        }

        $regexp = '[^/]+';
        $defaultValue = null;

        if ($rawGroupName[2] != '}') {
          # detect default parameter
          $parameterInfo = substr($rawParameterInfo, strlen($rawGroupName[0]), -1);


          $rawRegexpAndValue = preg_split('#(?<!\\\)\:#', $parameterInfo, -1, PREG_SPLIT_DELIM_CAPTURE);


          $count = count($rawRegexpAndValue);

          if ($count > 2) {
            throw new \InvalidArgumentException("Cant detect default value and regexp for parameter:" . $name);
          }


          if (isset($rawRegexpAndValue[1])) {
            $defaultValue = preg_replace('#(\\\\)([:\{\}])#', '$2', $rawRegexpAndValue[1]);
          }

          if ($defaultValue !== null and empty($rawRegexpAndValue[0])) {
            throw new \InvalidArgumentException('Please specify regexp for parameter:' . $name);
          }


          if (!empty($rawRegexpAndValue[0])) {
            $regexp = $rawRegexpAndValue[0];
          }

          if ($defaultValue !== null) {
            $regexp = $regexp . '|';
          }

        }


        $replaceToString = '(?P<' . $name . '>' . $regexp . ')$1';

        if ($defaultValue !== null) {
          $replaceToString .= '?';
        }

        $this->templateMatch = preg_replace('!' . preg_quote($rawParameterInfo) . '(.)!', $replaceToString, $this->templateMatch);
        $this->templateBuild = str_replace($rawParameterInfo, '{' . $name . '}', $this->templateBuild);

        $parameters[$name] = [
          $regexp,
          $defaultValue,
        ];

      }


      $this->parameters = $parameters;

    }


    /**
     * @param string $input
     * @param null $matchedParams
     * @return bool
     */
    public function match($input, &$matchedParams = null) {
      $matchedParams = array();

      if (preg_match('!^' . $this->templateMatch . '$!', $input, $match)) {
        foreach ($match as $matchId => $matchValue) {
          if (is_string($matchId)) {
            $matchedParams[$matchId] = $matchValue;
          }
        }


        foreach ($this->parameters as $name => $parameter) {
          if ($parameter[1] == null) {
            continue;
          }

          if (isset($matchedParams[$name]) and $matchedParams[$name] !== '') {
            continue;
          }

          $matchedParams[$name] = $parameter[1];
        }

        return true;
      } else {
        return false;
      }
    }


    /**
     * @param array $parameters
     * @param array $usedParameters
     * @return string
     */
    public function build(array $parameters = array(), &$usedParameters = null) {
      $usedParameters = array();
      $path = $this->templateBuild;

      foreach ($this->parameters as $name => $parameterInfo) {
        $regexp = $parameterInfo[0];
        $defaultValue = $parameterInfo[1];

        $value = (isset($parameters[$name])) ? $parameters[$name] : null;


        if ($value == null and $defaultValue === null) {
          throw new \InvalidArgumentException("Require parameter:" . $name);
        }


        if ($value == $defaultValue or $value == null) {
          # skip default values
          $usedParameters[$name] = $name;
          $path = preg_replace('!\{' . $name . '\}.?!', '', $path);
          continue;
        }

        # not empty value 
        if (!preg_match('~' . $regexp . '~', $value)) {
          throw new \InvalidArgumentException("Invalid parameter: " . $name);
        }

        $usedParameters[$name] = $name;
        $path = preg_replace('!\{' . $name . '\}!', $value, $path);
      }


      return $path;

    }


    /**
     * @return string
     */
    public function getTemplateMatch() {
      return $this->templateMatch;
    }


    /**
     * @return array
     */
    public function getParameters() {
      return $this->parameters;
    }


    /**
     * @return string
     */
    public function getTemplateBuild() {
      return $this->templateBuild;
    }

  }
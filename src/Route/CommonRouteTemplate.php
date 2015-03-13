<?

  namespace ReRoute\Route;

  /**
   *
   * @package ReRoute\Route
   */
  class CommonRouteTemplate {

    /**
     * @var string
     */
    protected $template = null;

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

      $this->template = $template;
      preg_match_all('![\{\}]!', $template, $delimiters, PREG_OFFSET_CAPTURE);


      $delimiters = $delimiters[0];

      # 1. skip quoted delimiters
      foreach ($delimiters as $key => $value) {
        $symbol = mb_substr($template, $value[1] - 1, 1);
        if ($symbol == '\\') {
          unset($delimiters[$key]);
        }
      }

      $delimiters = array_values($delimiters);

      $delimitersNum = count($delimiters);

      if ($delimitersNum % 2 !== 0) {
        throw new \InvalidArgumentException("Invalid template. Different number of delimiters");
      }


      $parameters = [];
      $previousStartIndex = null;
      $state = null;

      if ($delimiters[0][0] !== '{') {
        throw new \InvalidArgumentException("Invalid group start");
      }


      if ($delimiters[($delimitersNum - 1)][0] !== '}') {
        throw new \InvalidArgumentException("Invalid group end");
      }

      foreach ($delimiters as $value) {
        if ($state == null) {
          $state = 1;
          $previousStartIndex = $value[1];
          continue;
        }

        if ($value[0] == '}') {
          $state--;
        } else {
          $state++;
        }


        if ($state == 0 and $previousStartIndex !== null) {
          $parameters[] = mb_substr($template, $previousStartIndex, $value[1] - $previousStartIndex + 1);
          $previousStartIndex = null;
        }

      }

      if ($delimitersNum / 2 != count($parameters)) {
        throw new \InvalidArgumentException('Cant detect groups from delimiters');
      }


      # rebuild groups with names and value params
      foreach ($parameters as $i => $value) {
        unset($parameters[$i]);

        preg_match('!^\{([a-z][0-9a-z]*)(:|\})!i', $value, $rawGroupName);


        if (empty($rawGroupName[1])) {
          throw new \InvalidArgumentException("Cant detect parameter name");
        }

        $name = $rawGroupName[1];

        if (isset($parameters[$name])) {
          throw new \InvalidArgumentException("Parameter with name already defined:" . $name);
        }

        $defaultRegex = '[^/]+';
        $defaultValue = null;


        if ($rawGroupName[2] != '}') {
          # detect default parameter
          $parameterInfo = substr($value, strlen($rawGroupName[0]), -1);
          $rawRegexpAndValue = preg_split('![^\\\\]:!', $parameterInfo);
          if (count($rawRegexpAndValue) > 2) {
            throw new \InvalidArgumentException("Cant detect default value and regexp for parameter:" . $name);
          }

          if (isset($rawRegexpAndValue[0])) {
            $defaultRegex = $rawRegexpAndValue[0];
          }

          if (isset($rawRegexpAndValue[1])) {
            $defaultValue = $rawRegexpAndValue[1];
          }
        }


        $this->template = str_replace($value, '{' . $name . '}', $this->template);

        $parameters[$name] = [
          $defaultRegex,
          $defaultValue,
        ];

      }

      $this->parameters = $parameters;

    }

    /**
     * @return string
     */
    public function getTemplate() {
      return $this->template;
    }

    /**
     * @return array
     */
    public function getParameters() {
      return $this->parameters;
    }

  }
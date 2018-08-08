<?php
  /*
   * status.php
   * @copyright Copyright 2008 - http://www.innov-concept.com
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @license GPL 2 License & MIT Licencse
   
  */


  namespace ClicShopping\Apps\Tools\ImportData\Module\ClicShoppingAdmin\Config\ID\Params;

  use ClicShopping\OM\HTML;

  class status extends \ClicShopping\Apps\Tools\ImportData\Module\ClicShoppingAdmin\Config\ConfigParamAbstract {
    public $default = 'True';
    public $sort_order = 10;

    protected function init() {
        $this->title = $this->app->getDef('cfg_import_data_status_title');
        $this->description = $this->app->getDef('cfg_import_data_status_description');
    }

    public function getInputField()  {
      $value = $this->getInputValue();

      $input =  HTML::radioField($this->key, 'True', $value, 'id="' . $this->key . '1" autocomplete="off"') . $this->app->getDef('cfg_import_data_status_true') . ' ';
      $input .=  HTML::radioField($this->key, 'False', $value, 'id="' . $this->key . '2" autocomplete="off"') . $this->app->getDef('cfg_import_data_status_false');

      return $input;
    }
  }
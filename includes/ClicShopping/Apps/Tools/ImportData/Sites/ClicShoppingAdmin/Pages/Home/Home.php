<?php
/*
 * Home.php
 * @copyright Copyright 2008 - http://www.innov-concept.com
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @license GPL 2 License & MIT Licencse

*/

  namespace ClicShopping\Apps\Tools\ImportData\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Tools\ImportData\ImportData;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_ImportData = new ImportData();
      Registry::set('ImportData', $CLICSHOPPING_ImportData);

      $this->app = Registry::get('ImportData');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }

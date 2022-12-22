<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\ImportData\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Tools\ImportData\ImportData;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      $CLICSHOPPING_ImportData = new ImportData();
      Registry::set('ImportData', $CLICSHOPPING_ImportData);

      $this->app = Registry::get('ImportData');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }

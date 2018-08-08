<?php
/*
 * Configure.php
 * @copyright Copyright 2008 - http://www.innov-concept.com
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @license GPL 2 License & MIT Licencse
*/

  namespace ClicShopping\Apps\Tools\ImportData\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class ImportConfigure extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_ImportData = Registry::get('ImportData');

      $this->page->setFile('import_configure.php');

      $CLICSHOPPING_ImportData->loadDefinitions('ClicShoppingAdmin/main');
    }
  }
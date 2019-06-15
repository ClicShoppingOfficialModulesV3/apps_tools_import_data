<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_ImportData = Registry::get('ImportData');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $import_array = array(array('id' => '0', 'text' => $CLICSHOPPING_ImportData->getDef('text_select')),
    array('id' => 'database', 'text' => $CLICSHOPPING_ImportData->getDef('database')),
    array('id' => 'file', 'text' => $CLICSHOPPING_ImportData->getDef('text_file'))
  );
?>

<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/migration.png', $CLICSHOPPING_ImportData->getDef('heading_title'), '40', '40'); ?></span>
          <span
            class="col-md-4 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_ImportData->getDef('heading_title'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>

  <div class="col-md-12 mainTitle"><strong><?php echo $CLICSHOPPING_ImportData->getDef('text_import_data'); ?></strong>
  </div>
  <div class="adminformTitle">
    <?php echo HTML::form('step_1', $CLICSHOPPING_ImportData->link('ImportConfigure')); ?>

    <div class="row">
      <div class="col-md-7">
        <div class="form-group row">
          <label for="<?php echo $CLICSHOPPING_ImportData->getDef('text_select_import'); ?>"
                 class="col-5 col-form-label"><?php echo $CLICSHOPPING_ImportData->getDef('text_select_import'); ?></label>
          <div class="col-md-5">
            <?php echo HTML::selectMenu('select_type_import', $import_array, '', 'onchange="this.form.submit();"'); ?>
          </div>
        </div>
      </div>
    </div>

    </form>
  </div>
  <div class="separator"></div>
  <div class="alert alert-info" role="alert">
    <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_ImportData->getDef('text_title_help')) . ' ' . $CLICSHOPPING_ImportData->getDef('text_title_help') ?></div>
    <div class="separator"></div>
    <div><?php echo $CLICSHOPPING_ImportData->getDef('text_help_description'); ?></div>
  </div>

</div>
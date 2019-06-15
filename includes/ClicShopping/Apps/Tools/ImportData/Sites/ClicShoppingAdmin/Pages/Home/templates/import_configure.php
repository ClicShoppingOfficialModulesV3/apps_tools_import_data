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

  use ClicShopping\Apps\Tools\ImportData\Classes\ClicShoppingAdmin\ImportDatabase;

  $CLICSHOPPING_ImportData = Registry::get('ImportData');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
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
          <span class="col-md-7 text-md-right">
<?php
  echo HTML::button($CLICSHOPPING_ImportData->getDef('button_cancel'), null, $CLICSHOPPING_ImportData->link('ImportData'), 'danger') . ' ';
  echo HTML::form('step_2', $CLICSHOPPING_ImportData->link('ImportProcess'));
  echo HTML::button($CLICSHOPPING_ImportData->getDef('button_process'), null, null, 'success');
?>
           </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <?php
    if ($_POST['select_type_import'] == 'database') {
      ?>


      <div class="col-md-12 mainTitle">
        <strong><?php echo $CLICSHOPPING_ImportData->getDef('text_title_step2'); ?></strong></div>
      <div class="adminformTitle">

        <div class="row">
          <div class="col-md-5">
            <div class="form-group row">
              <label for="<?php echo $CLICSHOPPING_ImportData->getDef('server'); ?>"
                     class="col-5 col-form-label"><?php echo $CLICSHOPPING_ImportData->getDef('server'); ?></label>
              <div class="col-md-5">
                <?php echo HTML::inputField('server', '', 'required aria-required="true" placeholder="' . $CLICSHOPPING_ImportData->getDef('server') . '"'); ?>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-5">
            <div class="form-group row">
              <label for="<?php echo $CLICSHOPPING_ImportData->getDef('database'); ?>"
                     class="col-5 col-form-label"><?php echo $CLICSHOPPING_ImportData->getDef('database'); ?></label>
              <div class="col-md-5">
                <?php echo HTML::inputField('database', '', 'required aria-required="true" placeholder="' . $CLICSHOPPING_ImportData->getDef('database') . '"'); ?>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-5">
            <div class="form-group row">
              <label for="<?php echo $CLICSHOPPING_ImportData->getDef('username'); ?>"
                     class="col-5 col-form-label"><?php echo $CLICSHOPPING_ImportData->getDef('username'); ?></label>
              <div class="col-md-5">
                <?php echo HTML::inputField('username', '', 'required aria-required="true" placeholder="' . $CLICSHOPPING_ImportData->getDef('username') . '"'); ?>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-5">
            <div class="form-group row">
              <label for="<?php echo $CLICSHOPPING_ImportData->getDef('password'); ?>"
                     class="col-5 col-form-label"><?php echo $CLICSHOPPING_ImportData->getDef('password'); ?></label>
              <div class="col-md-5">
                <?php echo HTML::inputField('password', '', 'id="password" required aria-required="true" placeholder="' . $CLICSHOPPING_ImportData->getDef('password') . '"', 'password'); ?>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-5">
            <div class="form-group row">
              <label for="<?php echo $CLICSHOPPING_ImportData->getDef('prefix_tables'); ?>"
                     class="col-5 col-form-label"><?php echo $CLICSHOPPING_ImportData->getDef('prefix_tables'); ?></label>
              <div class="col-md-5">
                <?php echo HTML::inputField('prefix_tables', '', 'placeholder="' . $CLICSHOPPING_ImportData->getDef('prefix_tables') . '"'); ?>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-5">
            <div class="form-group row">
              <label for="<?php echo $CLICSHOPPING_ImportData->getDef('select_ecommerce_solution'); ?>"
                     class="col-5 col-form-label"><?php echo $CLICSHOPPING_ImportData->getDef('select_ecommerce_solution'); ?></label>
              <div class="col-md-5">
                <?php echo ImportDatabase::ImportSolution('select_ecommerce_solution') . HTML::hiddenField('ecommerce_solution', 'ecommerce_solution'); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      </form>
      <?php
    }
  ?>
  <div class="separator"></div>
  <div class="alert alert-info" role="alert">
    <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_ImportData->getDef('text_title_help')) . ' ' . $CLICSHOPPING_ImportData->getDef('text_title_help') ?></div>
    <div class="separator"></div>
    <div><?php echo $CLICSHOPPING_ImportData->getDef('text_help_description'); ?></div>
  </div>
</div>
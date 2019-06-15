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
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
  $CLICSHOPPING_Hooks = Registry::get('Hooks');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  $error = false;

  $server = HTML::sanitize($_POST['server']);
  $base = HTML::sanitize($_POST['database']);
  $user = HTML::sanitize($_POST['username']);
  $pass = HTML::sanitize($_POST['password']);
  $prefix_tables = HTML::sanitize($_POST['prefix_tables']);

  $mysqli = @new \mysqli($server, $user, $pass, $base);
  @$mysqli->set_charset("utf8mb4");

  if ($mysqli->connect_error) {
    $error = true;
  }

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
  <?php
    if ($error === true) {
      ?>
      <div class="alert alert-warning" role="alert">
        <?php
          echo '<div>' . $CLICSHOPPING_ImportData->getDef('text_error_connexion') . '</div>';
          echo '<div class="text-md-right">' . HTML::button($CLICSHOPPING_ImportData->getDef('button_back'), null, $CLICSHOPPING_ImportData->link('ImportData&ImportConfigure'), 'warning') . '</div>';
          exit;
        ?>
      </div>
      <?php
    } else {
      ?>
      <div class="alert alert-success"
           role="alert"><?php echo $CLICSHOPPING_ImportData->getDef('text_success_connexion'); ?></div>

      <div class="separator"></div>
      <div class="col-md-12 mainTitle"><strong><?php echo $CLICSHOPPING_ImportData->getDef('text_process'); ?></strong>
      </div>
      <div class="adminformTitle">
        <?php
          echo '<div class="text-md-center text-danger">' . $CLICSHOPPING_ImportData->getDef('process_is_running_please_wait') . '</div>';
          echo '<div class="separator"></div>';
          echo '<div class="page-header"><h1>' . HTML::sanitize($_POST['select_ecommerce_solution']) . '</h1></div>';
          echo '<hr>';

          $import = HTML::sanitize($_POST['select_ecommerce_solution']);
          $CLICSHOPPING_Hooks->call('ImportDatabase', $import);
        ?>
      </div>
      <?php
    }
  ?>
</div>
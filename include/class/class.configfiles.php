<?php
/**
 * this File is part of OpenVPN-Admin - (c) 2020 OpenVPN-Admin
 *
 * NOTICE OF LICENSE
 *
 * GNU AFFERO GENERAL PUBLIC LICENSE V3
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/agpl-3.0.en.html
 *
 * Original Script from: https://github.com/Chocobozzz/OpenVPN-Admin
 *
 * @fork      https://github.com/Wutze/OpenVPN-Admin
 * @author    Wutze
 * @copyright 2020 OpenVPN-Admin
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');


/**
 * load/save config.files, create zip files
 * the main function allows only defined calls - see var $go
 *
 * @return 
 */

class config_files{
  var $go = array('save'=>'save',
                  'print'=>'print',
                  'loadzip'=>'loadzip');
  var $files = array('server'=>'server',
                  'win'=>'win',
                  'lin'=>'lin',
                  'osx'=>'osx');
  var $zipfile = array(
                  'osx' => 'osx',
                  'win' => 'win',
                  'lin' => 'lin');
  
  var $config_full_path = "../vpn/conf/server/";

  /**
   * main function
   */
  function main(){


#debug($this);


    (array_key_exists($this->action,$this->go)) ? $this->gotox = $this->go[$this->action] : $this->gotox = 'ERROR';
    ($this->isuser) ? '' : $this->gotox = 'ERROR';
    switch($this->gotox){
      case "save";


      break;

      case "loadzip";

      break;

      case "print";

      break;

      case "ERROR";
        echo "error";
    #debug($_SESSION,$this->isuser);
      break;
    }
  }

  /**
   * read historical config documents
   * @return html formatted history Conf-Files
   */
  function getHistory($cfg_file) {
    ?>
    <div class="alert alert-info" role="alert"><b>History</b>
      <div class="panel-group" id="accordion<?php echo Session::GetVar('session_id'); ?>">
        <?php foreach (array_reverse(glob('../vpn/conf/'.basename(pathinfo($cfg_file, PATHINFO_DIRNAME)).'/history/*'),true) as $i => $file): ?>
        <div class="panel panel-primary">
          <div class="panel-primary">
            <a data-toggle="collapse" data-parent="#accordion<?php echo Session::GetVar('session_id'); ?>" href="#collapse<?php echo Session::GetVar('session_id'); ?>-<?php echo $i ?>">
            <?php
              $history_file_name = basename($file);
              $chunks = explode('_', $history_file_name);
              echo date('d.m.y', $chunks[0]);
            ?>
            </a>
          </div>
          <div id="collapse<?php echo Session::GetVar('session_id'); ?>-<?php echo $i ?>" class="panel-collapse collapse">
            <div class="position-relative p-3 bg-gray" style="height: 180px">
              <div class="ribbon-wrapper ribbon-lg">
                <div class="ribbon bg-danger">
                  <?php echo date("d.m.y",$chunks[0]); ?>
                </div>
              </div>
              <div class="well">
                <pre>
                  <?php echo readfile($file) ?>
                </pre>
              </div>
            </div>
          </div>
          
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php
    #return;
  }


  /**
   *  [update_config] => true
      [config_file] => ../vpn/conf/server/server.conf
      [config_content]
    * auf _POST reagieren?! Alter, der tickt nicht richtig!
  */

  function save_config(){
  #debug($_POST);
    $pathinfo = pathinfo($this->file);
  
    $config_full_uri = $this->file; // the complete path to the file, including the file (name) its self and the fully qualified path
    #$config_full_path = "../vpn/conf/server/"; // path to file (without filename its self)
    $config_name = basename($_POST['config_file']); // config file name only (without path)
    $config_parent_dir = basename($this->config_full_path); // name of the dir that contains the config file (without path)
  
    /*
     * create backup for history
     *
     */
    if (!file_exists($dir="$this->config_full_path/history"))
       mkdir($dir, 0777, true);
    $ts = time();
    copy("$config_full_uri", "$this->config_full_path/history/${ts}_${config_name}");
  
    /*
     *  write config
     */
    $conf_success = file_put_contents($_POST['config_file'], $_POST['config_content']);
  
    echo json_encode([
      'debug' => [
          'config_file' => $_POST['config_file'],
          'config_content' => $_POST['config_content']
      ],
      'config_success' => $conf_success !== false,
    ]);
  }

  function configfiles(){
    ?>
    <div class="tab-pane fade position-relative p-3 bg-light" id="config" role="tabpanel" aria-labelledby="config-tab">
    <div class="ribbon-wrapper ribbon-lg">
      <div class="ribbon bg-primary">
        <?php echo GET_Lang::nachricht('_SERVER_CONFIG'); ?>
      </div>
    </div>
    <fieldset>
      <form class="save-form" method="post">
        <p>Attention! Restart server after changes!<p/>
        <textarea class="alert-danger form-control" data-config-file="<?= $cfg_file='../vpn/conf/server/server.conf' ?>" name="" id="" cols="30" rows="20"><?= file_get_contents($cfg_file) ?></textarea>
        
      </form>
    </fieldset>
    <?php echo getHistory($cfg_file) ?>
  </div>
  <?php 
  }

  /**
  * set value
  * @return defined vars for this class
  */
  function set_value($key, $val){
      $this->$key = $val;
  }

}





?>
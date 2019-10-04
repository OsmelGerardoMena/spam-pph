<?php



/**

    Plugin Name:	Spam Filter PPH

	Plugin URI:		

	Description:	Plugin

	Version: 		0.1

	Author:			Osmel Mena

	Author URI:		

	License:		GPLv2 or later

 **/

////Create table bd 
function spam_install(){
    global $wpdb; 

    ///Table Info
    $table_name= $wpdb->prefix . "spam_pph";
    $sql = " CREATE TABLE $table_name(
        id mediumint( 9 ) NOT NULL AUTO_INCREMENT ,
        creator int(11) NOT NULL ,
        creation_date datetime,
        creation_ip text,
        modifier int(11) NOT NULL ,
        modification_date datetime,
        modification_ip text,
        type int(2),
        value text,
        PRIMARY KEY ( `id` )    
    ) ;";
    $wpdb->query($sql);

    ///Table Prov
    $table_name_p= $wpdb->prefix . "spam_prov";
    $sql_p = " CREATE TABLE $table_name_p(
        id mediumint( 9 ) NOT NULL AUTO_INCREMENT ,
        user text NOT NULL ,
        date datetime,
        url text,
        PRIMARY KEY ( `id` )    
    ) ;";
    $wpdb->query($sql_p);

}

add_action('activate_spam-pph/spam-filter.php','spam_install');

////Eliminate table bd 
function spam_desinstall(){
    global $wpdb; 
    ///Table Info
    $table_name = $wpdb->prefix . "spam_pph";
    $sql = "DROP TABLE $table_name";
    $wpdb->query($sql);


    ///Table Prov
    $table_name_p = $wpdb->prefix . "spam_prov";
    $sql_p = "DROP TABLE $table_name_p";
    $wpdb->query($sql_p);
}

add_action('deactivate_spam-pph/spam-filter.php', 'spam_desinstall');


////Add archive css in wordpress
function admin_spam_style() {
    wp_enqueue_style('spam-styles', WP_PLUGIN_URL.'/spam-pph/css/spam-shortcode.css');
}
add_action('admin_enqueue_scripts', 'admin_spam_style');


////Function insert/update rows ips and emails
function save_ip_callback(){

    global $wpdb; 
    $table_name= $wpdb->prefix . "spam_pph";
    $user = wp_get_current_user();

    if ( count( $_POST['ips'] )>0 ) {        
        for($gg=0;$gg<count($_POST['ips']);$gg++){
            if($_POST['ips'][$gg]!=''){
                if($_POST['id_ip'][$gg]!=''){
                    $sql = " update $table_name  set modifier='".$user->ID."',modification_date='".date('Y-m-d')."',modification_ip='".$_SERVER['REMOTE_ADDR']."',value='".$_POST['ips'][$gg]."' where id='".$_POST['id_ip'][$gg]."' ";
                }
                else
                {
                    $sql = " insert into $table_name (creator,creation_date,creation_ip,value,type) value ('".$user->ID."','".date('Y-m-d')."','".$_SERVER['REMOTE_ADDR']."','".$_POST['ips'][$gg]."','1') ";

                }
                $wpdb->query($sql);
            }

        }	        
    }


    if ( count( $_POST['emails'] )>0 ) {	
        for($gg=0;$gg<count($_POST['emails']);$gg++){
            if($_POST['emails'][$gg]!=''){
                if($_POST['id_email'][$gg]!=''){
                    $sqll = " update $table_name  set modifier='".$user->ID."',modification_date='".date('Y-m-d')."',modification_ip='".$_SERVER['REMOTE_ADDR']."',value='".$_POST['emails'][$gg]."' where id='".$_POST['id_email'][$gg]."' ";
                }
                else
                {
                    $sqll = " insert into $table_name (creator,creation_date,creation_ip,value,type) value ('".$user->ID."','".date('Y-m-d')."','".$_SERVER['REMOTE_ADDR']."','".$_POST['emails'][$gg]."','2') ";   
                }
                $wpdb->query($sqll);
            }
        } 
    }

    wp_redirect(get_site_url().'/wp-admin/admin.php?page=admin.php?page=importar_spam');
    exit();
}
add_action('wp_ajax_nopriv_save_ip','save_ip_callback');
add_action('wp_ajax_save_ip','save_ip_callback');


////Create option create/edit ips and emails
add_action('admin_menu', 'my_admin_spam'); 
function my_admin_spam() { 
    add_menu_page('Spam Filter', 'Spam Filter', 'manage_options', 'admin.php?page=importar_spam','importar_spam');
}

////Create Display create/edit ips and emails
function importar_spam(){

    global $wpdb;
    $table_name = $wpdb->prefix . "spam_pph";
    $ips= $wpdb->get_results("SELECT * FROM $table_name where  type=1 order by creation_date asc ", OBJECT);
    $emails= $wpdb->get_results("SELECT * FROM $table_name where  type=2 order by creation_date asc ", OBJECT );


?>
<div class="wrap">
    <form method="POST" action="<?php echo admin_url('admin-ajax.php');?>" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_ip" />
        <table width="100%">
            <tr>
                <td width="50%" style="text-align: center;">
                    <h3>Ips</h3>
                    <table width="100%">
                        <tr class="multi-field-wrapper">		        
                            <td width="100%" style="padding:11px;border:1px solid #ccc">
                                <button type="button" class="add-field">Add</button>        
                                <div class="multi-fields">

                                    <?php if(count($ips)>0){
        foreach ( $ips as $fila ):
                                    ?>
                                    <div class="multi-field">
                                        <input type="text" name="ips[]" style="width:80%"  value="<?php echo $fila->value?>" class="textbox">

                                        <input type="hidden" name="id_ip[]" style="width:80%"  value="<?php echo $fila->id?>" class="">
                                        <button type="button" class="remove-field">-</button>
                                    </div>
                                    <?php endforeach;?>

                                    <?php }else{?>
                                    <div class="multi-field">
                                        <input type="text" name="ips[]" style="width:80%"  class="textbox">
                                        <button type="button" class="remove-field">-</button>
                                    </div>
                                    <?php }?>  
                                </div>
                            </td>	  
                        </tr>
                    </table>
                </td>

                <td width="50%" style="text-align: center;">
                    <h3>Emails</h3>
                    <table width="100%">
                        <tr class="multi-field-wrapperr">		        
                            <td width="100%" style="padding:11px;border:1px solid #ccc">
                                <button type="button" class="add-fieldd">Add</button>        
                                <div class="multi-fieldss">

                                    <?php if(count($emails)>0){
        foreach ( $emails as $fila ):  
                                    ?>
                                    <div class="multi-fieldd">
                                        <input type="text" name="emails[]" style="width:80%"  value="<?php echo $fila->value?>" class="textbox">
                                        <input type="hidden" name="id_email[]" style="width:80%"  value="<?php echo $fila->id?>" class="">
                                        <button type="button" class="remove-fieldd">-</button>
                                    </div>
                                    <?php endforeach;?>

                                    <?php }else{?>
                                    <div class="multi-fieldd">
                                        <input type="text" name="emails[]" style="width:80%"  class="textbox">
                                        <button type="button" class="remove-fieldd">-</button>
                                    </div>
                                    <?php }?>  
                                </div>
                            </td>	  
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br />
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save"></p>
    </form>
</div>
<?php
    wp_enqueue_script( 'spam-shortcode', WP_PLUGIN_URL . '/spam-pph/js/spam-shortcode.js');
}



////Create option Syncronize
add_action('admin_menu', 'my_admin_spam_sync'); 
function my_admin_spam_sync() { 
    add_menu_page('Spam Filter Synchronize', 'Spam Filter Synchronize', 'manage_options', 'admin.php?page=spam_synchronize','spam_synchronize');
}

////Create Display Syncronize Data
function spam_synchronize(){
    $prov = get_option( 'prov' );
    $user = wp_get_current_user();
?>
<div class="wrap">
    <?php if($_GET['syn']){?>
    <h2>Syncronitation succesfull</h2>
    <?php }?>
    <table width="100%">
        <tr>
            <td width="50%" style="text-align: center;">
                <form method="POST" action="<?php echo admin_url('admin-ajax.php');?>" enctype="multipart/form-data" style="text-align: center;">
                    <input type="hidden" name="action" value="save_prov" />
                    <h3>Proveedor</h3>
                    <input type="text" name="prov" style="width:40%"  value="<?php echo $prov?>" class="textbox">
                    <br />
                    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save"></p>
                </form>
            </td>
            <td width="50%" style="text-align: center;">
                <form method="POST" action="<?php echo admin_url('admin-ajax.php');?>" enctype="multipart/form-data" style="text-align: center;">
                    <input type="hidden" name="action" value="spam_prov" />
                    <input type="hidden" name="url" value="<?php echo site_url();?>" />
                    <input type="hidden" name="date" value="<?php echo date('Y-m-d H:m:s')?>" />
                    <input type="hidden" name="user" value="<?php echo $user->display_name?>" />
                    <input type="hidden" name="prov" value="<?php echo $prov;?>" />
                    <br />
                    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Synchronize"></p>
                </form>
            </td>
        </tr>
    </table>
</div>
<?php
}


////Function insert/update rows ips and emails
function save_prov_callback(){

    if ( isset( $_POST['prov'] ) ) {	
        update_option('prov', $_POST['prov']);		        
    }

    wp_redirect(get_site_url().'/wp-admin/admin.php?page=admin.php?page=spam_synchronize');
    exit();
}
add_action('wp_ajax_nopriv_save_prov','save_prov_callback');
add_action('wp_ajax_save_prov','save_prov_callback');



function spam_prov_callback(){

    global $wpdb; 
    $table_name= $wpdb->prefix . "spam_pph";

    $prov=$_POST['prov'];
    $date=$_POST['date'];
    $user=$_POST['user'];
    $url=$_POST['url'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $prov);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);

    $data = array(
        'action' => 'spam_synchronize',
        'user' => $user,
        'date' => $date,
        'url' => $url
    );

    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch);
    curl_close($ch);

    foreach (json_decode($output) as $key => $value) {
        
        $data= $wpdb->get_results("SELECT * FROM $table_name where  type='".$value->type."'  and value='".$value->value."'  ", OBJECT);
        $data_t=count($data);
        
        if($data_t>0){ 
            $sqll = " update $table_name  set modifier='".$value->modifier."',modification_date='".$value->modification_date."',modification_ip='".$value->modification_ip."',value='".$value->value."' where id='".$value->id."' ";
        }
        else
        {
            $sqll = " insert into $table_name (creator,creation_date,creation_ip,value,type) value ('".$value->creator."','".$value->creation_date."','".$value->creation_ip."','".$value->value."','".$value->type."') "; 

        }
        $wpdb->query($sqll);

    }

    wp_redirect(get_site_url().'/wp-admin/admin.php?page=admin.php?page=spam_synchronize&syn=1');

    exit();
}
add_action('wp_ajax_nopriv_spam_prov','spam_prov_callback');
add_action('wp_ajax_spam_prov','spam_prov_callback');


function spam_synchronize_callback(){

    global $wpdb; 
    $table_name= $wpdb->prefix . "spam_prov";
    $table_name_spam= $wpdb->prefix . "spam_pph";

    $data= $wpdb->get_results("SELECT * FROM $table_name where  user='".$_POST['user']."'", OBJECT);
    $data_t=count($data);

    if($data_t>0){
        $sqll = " update $table_name set date='".$_POST['date']."' where user='".$_POST['user']."'";   
    }
    else
    {
        $sqll = " insert into $table_name (user,date,url) value ('".$_POST['user']."','".$_POST['date']."','".$_POST['url']."') ";   

    }
    $wpdb->query($sqll);

    $rows= $wpdb->get_results("SELECT * FROM $table_name_spam where 1 order by creation_date asc ", OBJECT );

    $retun=json_encode($rows);

    echo $retun;
    exit();
}
add_action('wp_ajax_nopriv_spam_synchronize','spam_synchronize_callback');
add_action('wp_ajax_spam_synchronize','spam_synchronize_callback');




require_once dirname( __FILE__ ) . '/includes/spam-shortcode.php';


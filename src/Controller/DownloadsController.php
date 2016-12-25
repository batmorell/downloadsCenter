<?php
namespace App\Controller;
use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Utility\Text;
use Cake\Core\Configure;


/**
 * Personal Controller
 * User personal interface
 *
 */
class DownloadsController extends AppController {

    public function index() {
        $this->viewBuilder()->layout('main_layout');

        $session=$this->request->session();

        $lien = 'https://api.t411.li/auth ';

        $requete_post = array(
            'username' => Configure::read('T411.username'),
            'password' => Configure::read('T411.password')
        );

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $lien);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requete_post);

        $resultat = curl_exec($curl);
        curl_close($curl);

        $json = json_decode($resultat);
        $token = $json->token;

        $session->write('token', $token);
        $this->redirect(["controller"=>"Downloads","action"=>"research"] );
    }

    public function register() {
        $this->viewBuilder()->layout('login_register_layout');

        $session=$this->request->session();

        if($this->request->is("post"))
        {
            $this->loadModel("Players");
            $usr_id=$this->Players->savePlayer($this->request->data["email"], $this->request->data["password"]);
            $session->write("usr_id", $usr_id);
            $this->redirect(["controller"=>"Downloads","action"=>"index"] );
        }
    }

    public function listOfDownloads() {
        $this->viewBuilder()->layout('main_layout');

        $session=$this->request->session();

        $listOfDownloads = shell_exec("/var/www/downloadsCenter/webroot/list.sh");

        $re = '/(\s\d\s)(\d{1,3}%)/';
        $subst = ' | $2';

        $result = preg_replace($re, $subst, $listOfDownloads);

        //echo $result;

        $result = preg_split('/\|/', $result);

        print_r($result);

        $this->set('result', $result);
    }

    public function listOfFiles() {
        $this->viewBuilder()->layout('main_layout');

        $session=$this->request->session();

        $listOfDownloads = shell_exec("ls -1 webroot/downloads/11111");

        $re = '/\n./';
        $subst = ' | $0';

        $result = preg_replace($re, $subst, $listOfDownloads);
        
        $result = preg_split('/\|/', $result);

        $this->set('result', $result);

    }

    public function play() {
        $this->autoRender = false;
        $filePath = WWW_ROOT .'downloads/11111/The.Walking.Dead.S05E11.PROPER.VOSTFR.HDTV.XviD-ATeam/'. DS .'OutputFile.mp4';
        $this->response->file($filePath);
        return $this->response;
    }
    
    public function download($id) {
        $this->autoRender = false;
        $filePath = WWW_ROOT .'downloads/11111/'. DS . $id .'.pdf';
        $this->response->file($filePath ,
            array('download'=> true, 'name'=> 'file name'));
        return $this->response;
    }

    public function player() {
        $this->viewBuilder()->layout('main_layout');
        
    }

    public function research() {
        $this->viewBuilder()->layout('main_layout');

        $session=$this->request->session();
        $this->set('options',["968" => "Saison 01",
                "969" => "Saison 02",
                "970" => "Saison 03",
                "971" => "Saison 04",
                "972" => "Saison 05"]
        );


        if($this->request->is("post"))
        {

            switch ($this->request->data['form'])
            {
                case 'Research':
                    $search = $name = str_replace(' ', '+', $this->request->data['search']);
                    $lien = 'https://api.t411.li/torrents/search/' . $search . '?cid=433';
                    if ($this->request->data['season'] != null){
                        $lien = $lien . '&term[45][]=' . $this->request->data['season'];
                    }
                    $token = $session->read('token');
                    $curl = curl_init();

                    curl_setopt($curl, CURLOPT_URL, $lien);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3");
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: $token"));

                    $res = curl_exec($curl);
                    curl_close($curl);
                    $res = json_decode($res,true);
                    $this->set('result', $res);
                    break;

                case 'Action':
                    $lien = 'https://api.t411.li/torrents/download/'.$this->request->data['id'];
                    $token = $session->read('token');
                    $curl = curl_init();

                    curl_setopt($curl, CURLOPT_URL, $lien);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3");
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: $token"));

                    $res = curl_exec($curl);
                    curl_close($curl);
                    $this->set('result', $res);
                    $fp = fopen($this->request->data['id'].'.torrent', 'a');
                    fwrite($fp, $res);
                    fclose($fp);
                    $cmd = "/var/www/downloadsCenter/webroot/script.sh " . "11111" . $this->request->data['id'];
                    $message = shell_exec($cmd);
                    break;
                
                default:
                    # code...
                    break;
            }
        }
    }
    

    public function login() {

        $this->viewBuilder()->layout('login_register_layout');

        $session = $this->request->session();
        $session->destroy();
        $this->loadModel("Players");
        $this->loadModel("Fighters");

        $this->set("errormessage", NULL);

        if($this->request->is("post"))
        {
            if($this->Players->verifyLogin($this->request->data["email"], $this->request->data["password"])) {
                $usr_id = $this->Players->getID($this->request->data["email"]);
                $current_fighter_id = $this->Fighters->getFirstFighter($usr_id)['id'];
                $session->write("usr_id", $usr_id);
                $session->write("current_fighter_id", $current_fighter_id);
                $this->redirect(["controller" => "Arenas", "action" => "index"]);
            }
            else {
                $this->set("errormessage", "Error: false email or password.");
            }
        }
    }
}

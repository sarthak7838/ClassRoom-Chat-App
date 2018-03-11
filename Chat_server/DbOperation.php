<?php

class DbOperation
{
    //Database connection link
    private $con;

    //Class constructor
    function __construct()
    {
        //Getting the DbConnect.php file
        require_once dirname(__FILE__) . '/DbConnect.php';

        //Creating a DbConnect object to connect to the database
        $db = new DbConnect();

        //Initializing our connection link of this class
        //by calling the method connect of DbConnect class
        $this->con = $db->connect();
    }

    //storing token in database
    public function registerDevice($name,$email,$token){
        if(!$this->isEmailExist($email)){
            $stmt = $this->con->prepare("INSERT INTO devices (name,email, token) VALUES (?,?,?) ");
            $stmt->bind_param("sss",$name,$email,$token);
            if($stmt->execute())
                return 0; //return 0 means success
            return 1; //return 1 means failure
        }else{
            return 2; //returning 2 means email already exist
        }
    }

    //the method will check if email already exist
    private function isEmailexist($email){
        $stmt = $this->con->prepare("SELECT id FROM devices WHERE email = ?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    //getting all tokens to send push to all devices
    public function getAllTokens(){
        $stmt = $this->con->prepare("SELECT token FROM devices");
        $stmt->execute();
        $result = $stmt->get_result();
        $tokens = array();
        while($token = $result->fetch_assoc()){
            array_push($tokens, $token['token']);
        }
        return $tokens;
    }
    //Function to get the user with email
    public function getUser($email)
    {
        $stmt = $this->con->prepare("SELECT * FROM devices WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        return $user;
    }


    //getting a specified token to send push to selected device
    public function getTokenByEmail($email){
        $stmt = $this->con->prepare("SELECT token FROM devices WHERE email = ?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return array($result['token']);
    }

    //getting all the registered devices from database
    public function getAllDevices(){
        $stmt = $this->con->prepare("SELECT * FROM devices");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    //Function to add message to the database
    public function addMessage($id,$message){
        $stmt = $this->con->prepare("INSERT INTO messages (message,users_id) VALUES (?,?)");
        $stmt->bind_param("si",$message,$id);
        if($stmt->execute())
            return true;
        else
            return false;
    }

    //Function to get messages from the database
    public function getMessages(){
        $stmt = $this->con->prepare("SELECT a.id, a.message, a.sentat, a.users_id, b.name FROM messages a, devices b WHERE a.users_id = b.id ORDER BY a.id ASC;");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }
}

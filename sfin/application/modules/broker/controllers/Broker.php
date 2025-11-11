<?php
defined("BASEPATH") or exit("No direct script access allowed");
#------------------------------------
# Author: Cloudsubset
# Author link: https://www.cloudsubset.com/
# Dynamic style php file
# Developed by :Jensy
#------------------------------------

class Broker extends MX_Controller
{
    public function __construct(){
        parent::__construct();

        $this->load->model(["broker_model"]);
        if (!$this->session->userdata("isLogIn")) {
            redirect("login");
        }
    }

    function index(){
        $data["title"] = display("broker_list");
        $data["module"] = "broker";
        $data["page"] = "broker_list";
        $data["broker_dropdown"] = $this->broker_model->broker_dropdown();
        $data["all_broker"] = $this->broker_model->allbroker();
        echo modules::run("template/layout", $data);
    }

    public function cloudsubset_CheckbrokerList(){
        $postData = $this->input->post();
        $data = $this->broker_model->getbrokerList($postData);
        echo json_encode($data);
    }

    public function cloudsubset_form($id = null){
        $data["title"] = display("add_broker");
        #-------------------------------#
        $this->form_validation->set_rules("broker_name", display("broker_name"), "required|max_length[200]");
        $this->form_validation->set_rules("broker_mobile", display("broker_mobile"), "max_length[20]");
        if (empty($id)) {
            $this->form_validation->set_rules(
                "broker_email", display("email"), "max_length[100]|valid_email|is_unique[broker_information.email_address]"
            );
        } else {
            $this->form_validation->set_rules(
                "broker_email", display("email"), "max_length[100]|valid_email"
            );
        }
        $this->form_validation->set_rules("contact", display("contact"), "max_length[200]");
        $this->form_validation->set_rules("phone", display("phone"), "max_length[20]");
        $this->form_validation->set_rules("city", display("city"), "max_length[100]");
        $this->form_validation->set_rules("state", display("state"), "max_length[100]");
        $this->form_validation->set_rules("zip", display("zip"), "max_length[30]");
        $this->form_validation->set_rules("country", display("country"), "max_length[100]");
        $this->form_validation->set_rules("broker_address", display("broker_address"), "max_length[255]");
        $this->form_validation->set_rules("address2", display("address2"), "max_length[255]");

        #-------------------------------#

        $data["broker"] = (object) ($postData = ["broker_id" => $this->input->post("broker_id", true), "broker_name" => $this->input->post("broker_name", true), "mobile" => $this->input->post("broker_mobile", true), "emailnumber" => $this->input->post("broker_email", true), "email_address" => $this->input->post("email_address", true), "contact" => $this->input->post("contact", true), "phone" => $this->input->post("phone", true), "fax" => $this->input->post("fax", true), "city" => $this->input->post("city", true), "state" => $this->input->post("state", true), "zip" => $this->input->post("zip", true), "country" => $this->input->post("country", true), "address" => $this->input->post("broker_address", true), "address2" => $this->input->post("address2", true), "status" => 1, ]);
        #-------------------------------#
        if ($this->form_validation->run() === true) {
            #if empty $id then insert data
            if (empty($postData["broker_id"])) {
                if ($this->broker_model->create($postData)) {
                    #set success message

                    $info["msg"] = display("save_successfully");
                    $info["status"] = 1;
                } else {
                    #set exception message

                    $info["msg"] = display("please_try_again");
                    $info["status"] = 0;
                }
            } else {
                if ($this->broker_model->update($postData)) {
                    #set success message
                    $info["msg"] = display("update_successfully");
                    $info["status"] = 1;
                } else {
                    #set exception message
                    $info["msg"] = display("please_try_again");
                    $info["status"] = 0;
                }
            }

            echo json_encode($info);
        } else {
            if (empty($this->input->post("broker_name", true))) {
                if (!empty($id)) {
                    $data["title"] = display("edit_broker");
                    $data["broker"] = $this->broker_model->singledata($id);
                }
                $data["module"] = "broker";
                $data["page"] = "form";
                echo Modules::run("template/layout", $data);
            } else {
                $info["msg"] = validation_errors();
                $info["status"] = 0;
                echo json_encode($info);
            }
        }
    }

    public function cloudsubset_delete($id){
        if ($this->broker_model->delete($id)) {
            echo display("delete_successfully");
        } else {
            display("please_try_again");
        }
    }

    public function broker_search($id){
        $data["brokers"] = $this->broker_model->individual_info($id);
        $this->load->view("broker_search", $data);
    }

    public function cloudsubset_broker_ledger(){
        $data["title"] = display("broker_ledger");
        #-------------------------------#
        #
        #pagination starts
        #
        $config["base_url"] = base_url("broker_ledger");
        $config["total_rows"] = $this->broker_model->count_broker_ledger();
        $config["per_page"] = 10;
        $config["uri_segment"] = 2;
        $config["last_link"] = "Last";
        $config["first_link"] = "First";
        $config["next_link"] = "Next";
        $config["prev_link"] = "Prev";
        $config["full_tag_open"] = "<ul class='pagination col-xs pull-right'>";
        $config["full_tag_close"] = "</ul>";
        $config["num_tag_open"] = "<li>";
        $config["num_tag_close"] = "</li>";
        $config["cur_tag_open"] = "<li class='disabled'><li class='active'><a href='#'>";
        $config["cur_tag_close"] = "<span class='sr-only'></span></a></li>";
        $config["next_tag_open"] = "<li>";
        $config["next_tag_close"] = "</li>";
        $config["prev_tag_open"] = "<li>";
        $config["prev_tagl_close"] = "</li>";
        $config["first_tag_open"] = "<li>";
        $config["first_tagl_close"] = "</li>";
        $config["last_tag_open"] = "<li>";
        $config["last_tagl_close"] = "</li>";
        /* ends of bootstrap */
        $this->pagination->initialize($config);
        $page = $this->uri->segment(2) ? $this->uri->segment(2) : 0;
        $data["ledgers"] = $this->broker_model->broker_ledgerdata( $config["per_page"], $page);
        $data["links"] = $this->pagination->create_links();
        $data["broker"] = $this->broker_model->broker_list_ledger();
        $data["broker_name"] = "";
        $data["broker_id"] = "";
        $data["address"] = "";
        $data["module"] = "broker";
        $data["page"] = "broker_ledger";
        echo Modules::run("template/layout", $data);
    }

    public function cloudsubset_broker_ledgerData(){
        $start = $this->input->post("from_date", true);
        $end = $this->input->post("to_date", true);
        $broker_id = $this->input->post("broker_id", true);
        $broker_detail = $this->broker_model->broker_personal_data($broker_id);
        $data["title"] = display("broker_ledger");
        $data["broker"] = $this->broker_model->broker_list_ledger();
        $data["ledgers"] = $this->broker_model->brokerledger_searchdata( $broker_id, $start, $end);
        $data["broker_name"] = $broker_detail[0]["broker_name"];
        $data["broker_id"] = $broker_id;
        $data["address"] = $broker_detail[0]["address"];
        $data["start"] = $start;
        $data["end"] = $end;
        $data["module"] = "broker";
        $data["links"] = "";
        $data["page"] = "broker_ledger";
        echo Modules::run("template/layout", $data);
    }

    public function cloudsubset_broker_advance(){
        $data["title"] = display("broker_advance");
        $data["broker_list"] = $this->broker_model->broker_list_advance();
        $data["module"] = "broker";
        $data["page"] = "broker_advance";
        echo Modules::run("template/layout", $data);
    }

    public function insert_broker_advance(){
        $advance_type = $this->input->post("type", true);
        if ($advance_type == 1) {
            $dr = $this->input->post("amount", true);
            $tp = "d";
        } else {
            $cr = $this->input->post("amount", true);
            $tp = "c";
        }
        $createby = $this->session->userdata("id");
        $createdate = date("Y-m-d H:i:s");
        $transaction_id = $this->broker_model->generator(10);
        $broker_id = $this->input->post("broker_id", true);
        $brokerinfo = $this->db ->select("*") ->from("broker_information") ->where("broker_id", $broker_id) ->get() ->row();
        $headn = $broker_id . "-" . $brokerinfo->broker_name;
        $coainfo = $this->db ->select("*") ->from("acc_coa") ->where("broker_id", $broker_id) ->get() ->row();
        $broker_headcode = $coainfo->HeadCode;

        $broker_accledger = ["VNo" => $transaction_id, "Vtype" => "Advance", "VDate" => date("Y-m-d"), "COAID" => $broker_headcode, "Narration" => "broker Advance For  " . $brokerinfo->broker_name, "Debit" => !empty($dr) ? $dr : 0, "Credit" => !empty($cr) ? $cr : 0, "IsPosted" => 1, "CreateBy" => $this->session->userdata("id"), "CreateDate" => date("Y-m-d H:i:s"), "IsAppove" => 1, ];
        $cc = ["VNo" => $transaction_id, "Vtype" => "Advance", "VDate" => date("Y-m-d"), "COAID" => 111000001, "Narration" => "Cash in Hand  For " . $brokerinfo->broker_name . " Advance", "Debit" => !empty($dr) ? $dr : 0, "Credit" => !empty($cr) ? $cr : 0, "IsPosted" => 1, "CreateBy" => $this->session->userdata("id"), "CreateDate" => date("Y-m-d H:i:s"), "IsAppove" => 1, ];

        $this->db->insert("acc_transaction", $broker_accledger);
        $this->db->insert("acc_transaction", $cc);
        redirect(
            base_url(
                "broker_advance_receipt/" . $transaction_id . "/" . $broker_id
            ));
    }

    //broker_advance_receipt
    public function broker_advancercpt($receiptid = null, $broker_id = null){
        $data["title"] = display("advance_receipt");
        $broker_id = $this->uri->segment(3);
        $receiptdata = $this->broker_model->advance_details( $receiptid, $broker_id);
        $broker_details = $this->broker_model->broker_personal_data($broker_id);
        $data["details"] = $receiptdata;
        $data["broker_name"] = $broker_details[0]["broker_name"];
        $data["receipt_no"] = $receiptdata[0]["VNo"];
        $data["address"] = $broker_details[0]["address"];
        $data["mobile"] = $broker_details[0]["mobile"];
        $data["module"] = "broker";
        $data["page"] = "broker_advance_receipt";
        echo Modules::run("template/layout", $data);
    }

    public function cloudsubset_broker_ledgerinfo($broker_id){
        $broker_details = $this->broker_model->broker_personal_data($broker_id);
        $broker = $this->broker_model->broker_list_advance();
        $ledgers = $this->broker_model->broker_product_sale_info($broker_id);

        $data = ["title" => display("broker_ledger"), "ledgers" => $ledgers, "broker_id" => $broker_id, "broker_name" => $broker_details[0]["broker_name"], "address" => $broker_details[0]["address"], "broker" => $broker, "links" => "", ];

        $data["module"] = "broker";
        $data["page"] = "broker_ledger";
        echo Modules::run("template/layout", $data);
    }
}
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('transaction_model');
        $this->load->model('order_model');
        $this->load->model('product_model');
        $this->load->model('sizedetail_model');
        $this->load->model('size_model');
    }

    public function index() {
        $message_success = $this->session->flashdata('message_success');
        $this->data['message_success'] = $message_success;

        $message_fail = $this->session->flashdata('message_fail');
        $this->data['message_fail'] = $message_fail;

        $total = $this->transaction_model->get_total();
        $this->data['total'] = $total;

        $this->load->library('pagination');
        $config = array();
        $base_url = admin_url('transaction/index');
        $per = 10;
        $uri = 4;
        $config = pagination($base_url, $total, $per, $uri);


        $this->pagination->initialize($config);

        $segment = isset($this->uri->segments['4']) ? $this->uri->segments['4'] : NULL;
        $segment = intval($segment);

        $input['limit'] = array($config['per_page'], $segment);

        $input['order'] = array('created', 'DESC');
        $transaction = $this->transaction_model->get_list($input);
        $this->data['transaction'] = $transaction;



        $this->data['temp'] = 'admin/transaction/index';
        $this->load->view('admin/main', $this->data);
    }

    public function del() {
        $id = $this->uri->segment(4);
        $transaction = $this->transaction_model->get_info($id);

        if (empty($transaction)) {
            $this->session->set_flashdata('message_fail', 'Đơn đặt hàng không tồn tại');
            redirect(admin_url('transaction'));
        }
        $this->data['transaction'] = $transaction;
        if ($id != 0) {
            $transaction = $this->transaction_model->get_info($id);

            $input = array();
            $input['where'] = array('transaction_id' => $transaction->id);
            $info = $this->order_model->get_list($input);

            
            foreach ($info as $key => $value) {
                $sl = 0;
                //cộng số lượng size
                $input1['where'] = array('product_id' => $value->product_id, 'size_id' => $value->size_id);
                $size_detail = $this->sizedetail_model->get_list($input1);
                $sl = $sl +  $value->qty;
                if (sizeof($size_detail) != 0) {
                    $id_update_size = $size_detail[0]->id;
                    $amount = $size_detail[0]->quantity + $value->qty;
                    $data2 = array();
                    $data2 = array(
                        'product_id' => $value->product_id,
                        'size_id' => $value->size_id,
                        'quantity' => $amount,
                    );
                    $this->sizedetail_model->update($id_update_size, $data2);
                } else {
                    $data3 = array();
                    $data3 = array(
                        'product_id' => $value->product_id,
                        'size_id' => $value->size_id,
                        'quantity' => $value->qty,
                    );
                    $sl = $sl +  $value->qty;
                    $this->sizedetail_model->create($data3);
                }
                //Trừ lượt mua
                $product = $this->product_model->get_info($value->product_id);
                $data4 = array();
                $data4['buyed'] = $product->buyed - $sl;
                $this->product_model->update($value->product_id, $data4);
                
                $this->order_model->delete($value->id);
            }
            $this->transaction_model->delete($id);
            $this->session->set_flashdata('message_success', 'Xóa đơn đặt hàng thành công');
        } else {
            $this->session->set_flashdata('message_fail', 'Xóa đơn đặt hàng thất bại');
        }
        redirect(admin_url('transaction'));
    }

    public function detail() {
        $id = $this->uri->segment(4);
        $transaction = $this->transaction_model->get_info($id);
        $this->data['transaction'] = $transaction;

        $input = array();
        $input['where'] = array('transaction_id' => $transaction->id);
        $info = $this->order_model->get_list($input);

        $list_product = array();
        foreach ($info as $key => $value) {
            $this->db->select('`order`.`id` as `order_id`,`product`.`id` as `id`, `product`.`name` as `name`, `image_link`, `order`.`qty` as `qty`, `order`.`amount` as `price`, `sizes`.`name` as `size_name` ');
            $this->db->join('product', 'order.product_id = product.id');
            $this->db->join('sizes', 'order.size_id = sizes.id');
            $where = array();
            $where = array('order.id' => $value->id);
            $list_product[] = $this->order_model->get_info_rule($where);
        }
        $this->data['list_product'] = $list_product;
        $this->data['temp'] = 'admin/transaction/detail';
        $this->load->view('admin/main', $this->data);
    }

    public function deldetail() {
        $id = $this->uri->segment(4);
        $where = array();
        $where = array('id' => $id);
        if (!$this->order_model->check_exists($where)) {
            $this->session->set_flashdata('message_fail', 'Danh mục không tồn tại');
            redirect(admin_url('transaction'));
        }

        if ($id != 0) {
            $order = $this->order_model->get_info($id);
            //cộng số lượng
            $input1['where'] = array('product_id' => $order->product_id, 'size_id' => $order->size_id);
            $size_detail = $this->sizedetail_model->get_list($input1);

            if (sizeof($size_detail) != 0) {
                $id_update_size = $size_detail[0]->id;
                $amount = $size_detail[0]->quantity + $order->qty;
                $data2 = array();
                $data2 = array(
                    'product_id' => $order->product_id,
                    'size_id' => $order->size_id,
                    'quantity' => $amount,
                );
                $this->sizedetail_model->update($id_update_size, $data2);
            } else {
                $data3 = array();
                $data3 = array(
                    'product_id' => $order->product_id,
                    'size_id' => $order->size_id,
                    'quantity' => $order->qty,
                );
                $this->sizedetail_model->create($data3);
            }

            $this->order_model->delete($id);
            $transaction = $this->transaction_model->get_info($order->transaction_id);
            $data = array();
            $data['amount'] = $transaction->amount - $order->amount;
            $this->transaction_model->update($transaction->id, $data);
            $this->session->set_flashdata('message_success', 'Xóa thành công');
        } else {
            $this->session->set_flashdata('message_fail', 'Xóa thất bại');
        }
        redirect(admin_url('transaction'));
    }

    public function accept1() {
        $id = $this->uri->segment(4);
        $data = array();
        $data['status'] = '1';
        $this->transaction_model->update($id, $data);
        $this->session->set_flashdata('message_success', 'Xác nhận đơn đặt hàng thành công');
        redirect(admin_url('transaction'));
    }
    public function accept2() {
        $id = $this->uri->segment(4);
        $data = array();
        $data['status'] = '2';
        $this->transaction_model->update($id, $data);
        $this->session->set_flashdata('message_success', 'Xác nhận đang giao hàng');
        redirect(admin_url('transaction'));
    }
    public function accept3() {
        $id = $this->uri->segment(4);
        $data = array();
        $data['status'] = '3';
        $this->transaction_model->update($id, $data);
        $this->session->set_flashdata('message_success', 'Xác nhận giao hàng thành công');
        redirect(admin_url('transaction'));
    }

    
    // public function accept() {
    //     $id = $this->uri->segment(4);
    //     $status = $this->input->post('status'); // Assuming status is sent via POST request
    
    //     // Initialize data array with the status
    //     $data = array();
    //     $data['status'] = $status;
    
    //     // Update the transaction status
    //     $this->transaction_model->update($id, $data);
    
    //     // Set flash message based on status
    //     if ($status == 1) {
    //         $message = 'Xác nhận đơn đặt hàng thành công';
    //     } elseif ($status == 2) {
    //         $message = 'Xác nhận đang giao hàng';
    //     } elseif ($status == 3) {
    //         $message = 'Xác nhận giao hàng thành công';
    //     } else {
    //         $message = 'Trạng thái không hợp lệ';
    //     }
    
    //     // Set flash message and redirect
    //     $this->session->set_flashdata('message_success', $message);
    //     redirect(admin_url('transaction'));
    // }
    
    public function search() {
        if ($this->input->post('search') != null) {
            $input = array();
            $str = $this->input->post('search');
            $this->db->select('transaction.id as id,status,amount,user.name as user_name,date,user.phone as user_phone');
            $this->db->join('user', 'user.id = transaction.user_id');
            $this->db->where('user_name LIKE "%' . $str . '%" ');
            $transaction = $this->transaction_model->get_list($input);
            $this->data['transaction'] = $transaction;
            if (sizeof($transaction) == 0) {
                $this->session->set_flashdata('message_fail', 'Không tìm thấy');
                redirect(admin_url('transaction'));
            }
            $total = sizeof($transaction);
            $this->data['total'] = $total;
            $this->load->library('pagination');
            $config = array();
            $base_url = admin_url('transaction/search');
            $per = 10;
            $uri = 4;
            $config = pagination($base_url, $total, $per, $uri);
            $this->pagination->initialize($config);

            $segment = isset($this->uri->segments['4']) ? $this->uri->segments['4'] : NULL;
            $segment = intval($segment);

            $input['limit'] = array($config['per_page'], $segment);

            $this->data['temp'] = 'admin/transaction/search';
            $this->load->view('admin/main', $this->data);
        } else {
            redirect(admin_url('transaction'));
        }
    }

    
}

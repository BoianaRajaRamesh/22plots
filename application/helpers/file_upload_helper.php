<?php

function file_upload($folder_name, $file) {
    if (!is_dir('uploads/' . trim($folder_name))) {
        mkdir('uploads/' . trim($folder_name), 0777, true);
    }
    $config['upload_path'] = 'uploads/' . trim($folder_name) . '/';
    $config['allowed_types'] = 'jpg|png|pdf|svg';
    $config['max_size'] = 5096;
    $config['max_width'] = 0;
    $config['max_height'] = 0;
    $config['remove_spaces'] = true;
    $this->load->library('upload', $config);

    if (!$this->upload->do_upload($file)) {
        $err = $this->upload->display_errors();
        $data = array(
            'status' => "invalid",
            "message" => $err,
            "data" => array(),
        );
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    } else {
        return $this->upload->data('file_name');
    }
}

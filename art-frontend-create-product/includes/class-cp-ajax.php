<?php

class CP_Ajax
{

	public function __construct()
	{

		add_action('wp_ajax_created_event', [$this, 'callback']);
		add_action('wp_ajax_nopriv_created_event', [$this, 'callback']);
	}

	public function callback()
	{


		check_ajax_referer('cp-ajax-nonce', 'nonce');

		$this->validation();

		$this->validation_picture();


		$event_data = [
			'post_type'    => 'product',
			'post_status'  => 'publish',
			'post_title'   => sanitize_text_field($_POST['event_product']),
			'post_content' => wp_kses_post($_POST['event_descriptions']),
			'meta_input'   => [
				'date'     => sanitize_text_field($_POST['date']),
				'_price' => sanitize_text_field($_POST['_price']),

			],
			// 'tax_input'    => [
			// 	'topics'   => $_POST['event_type_product'],
			// 	'hashtags' => explode(',', sanitize_text_field($_POST['event_hashtags'])),

			// ],
		];

		$post_id = wp_insert_post($event_data);


		$this->upload_thumbnail($post_id);

		$this->set_meta($post_id, $event_data['meta_input']);

		$this->success('Событие `' . $post_id . '` успешно создано');

		wp_die();
	}


	public function upload_thumbnail($post_id)
	{

		if (empty($_FILES)) {
			return;
		}

		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/media.php');

		add_filter(
			'upload_mimes',
			function ($mimes) {

				return [
					'jpg|jpeg|jpe' => 'image/jpeg',
					'png'          => 'image/png',
				];
			}
		);

		$attachment_id = media_handle_upload('event_picture', $post_id);

		if (is_wp_error($attachment_id)) {
			$response_message = 'Ошибка загрузки файла `' . $_FILES['event_picture']['name'] . '`: ' . $attachment_id->get_error_message();
			$this->error($response_message);
		}

		set_post_thumbnail($post_id, $attachment_id);
	}



	public function set_meta($post_id, $data)
	{

		foreach ($data as $key => $value) {
			update_post_meta($post_id,  $key, $value,);
		}
	}


	function validation()
	{

		$error = [];

		$required = [
			'event_product'    => 'Это обязательное поле. Укажите название продукта ',
			//'event_topics'   => 'Это обязательное поле. Выберите нужную категорию',
			//'event_hashtags'     => 'Это обязательное поле. Укажите метку в виде хештега, в формате #вашаМетка',
			//'event_descriptions' => 'Это обязательное поле. Напишите о чем, это мероприятие',
			//'event_thumbnail'    => 'Это обязательное поле. Укажите миниатюру мероприятия',
			//'event_date'     => 'Это обязательное поле. Укажите дату мероприятия',
			//'event_location' => 'Это обязательное поле. Укажите меато проведения мероприятия',
		];

		foreach ($required as $key => $item) {

			if (empty($_POST[$key]) || !isset($_POST[$key])) {
				$error[$key] = $item;
			}
		}

		if ($error) {
			$this->error($error);
		}
	}

	public function validation_picture()
	{
		if (!empty($_FILES)) {
			$size     = getimagesize($_FILES['event_picture']['tmp_name']);
			$max_size = 1800;

			if ($size[0] > $max_size || $size[1] > $max_size) {
				$image_message = 'Изображение не может быть больше ' . $max_size . 'рх в высоту или ширину';
				$this->remove_image($image_message);
			}
		}
	}

	function success($message)
	{

		wp_send_json_success(
			[
				'response' => 'SUCCESS',
				'message'  => $message,
			]
		);
	}

	function error($message)
	{

		wp_send_json_error(
			[
				'response' => 'ERROR',
				'message'  => $message,
			]
		);
	}

	public function remove_image($image_message)
	{

		unlink($_FILES['event_thumbnail']['tmp_name']);

		$this->error($image_message);;
	}
}

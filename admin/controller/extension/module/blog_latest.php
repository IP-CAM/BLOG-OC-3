<?php
class ControllerExtensionModuleBlogLatest extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/blog_latest');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('blog_latest', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_blog_category'] = $this->language->get('entry_blog_category');
		$data['entry_source_type'] = $this->language->get('entry_source_type');
		$data['entry_style'] = $this->language->get('entry_style');
		$data['entry_margin'] = $this->language->get('entry_margin');
		$data['entry_column'] = $this->language->get('entry_column');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['source_type'])) {
			$data['error_source_type'] = $this->error['source_type'];
		} else {
			$data['error_source_type'] = '';
		}

		if (isset($this->error['category'])) {
			$data['error_category'] = $this->error['category'];
		} else {
			$data['error_category'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/blog_latest', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/blog_latest', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/blog_latest', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/blog_latest', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['style'])) {
			$data['style'] = $this->request->post['style'];
		} elseif (!empty($module_info['style'])) {
			$data['style'] = $module_info['style'];
		} else {
			$data['style'] = '';
		}

		if (isset($this->request->post['margin'])) {
			$data['margin'] = $this->request->post['margin'];
		} elseif (!empty($module_info['margin'])) {
			$data['margin'] = $module_info['margin'];
		} else {
			$data['margin'] = '';
		}

		if (isset($this->request->post['column'])) {
			$data['column'] = $this->request->post['column'];
		} elseif (!empty($module_info['column'])) {
			$data['column'] = $module_info['column'];
		} else {
			$data['column'] = '';
		}


		$data['type'] = array(
			'category' => $this->language->get('text_category'),
			'latest' => $this->language->get('text_latest'),
			'view' => $this->language->get('text_view'),
			'comment' => $this->language->get('text_comment'),
		);

		$data['style_type'] = $this->getLayout('blog_latest');

		if (isset($this->request->post['source_type'])) {
			$data['source_type'] = $this->request->post['source_type'];
		} elseif (!empty($module_info['source_type'])) {
			$data['source_type'] = $module_info['source_type'];
		} else {
			$data['source_type'] = '';
		}

		// Categories
		$this->load->model('xlm_blog/category');

		if (isset($this->request->post['blog_category'])) {
			$categories = $this->request->post['blog_category'];
		} elseif (!empty($module_info['blog_category'])) {
			$categories = $module_info['blog_category'];
		} else {
			$categories = array();
		}

		$data['blog_categories'] = array();

		foreach ($categories as $blog_category_id) {
			$category_info = $this->model_xlm_blog_category->getCategory($blog_category_id);

			if ($category_info) {
				$data['blog_categories'][] = array(
					'blog_category_id' => $category_info['blog_category_id'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/blog_latest', $data));
	}


	public function getLayout($name=null){
		$directory  = DIR_CATALOG.'view/theme/'.$this->config->get('theme_default_directory').'/template/extension/module/'.$name;
		if (is_dir($directory)) {
			$files = scandir($directory);
			foreach ($files as $value) {
				if (strpos($value, '.twig') == true) {
					list($fileName) = explode('.twig',$value);
					$templates[] = $fileName;
				}
			}
		}
		$templates = isset($templates) ? $templates : '';
		return $templates;
	}


	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/blog_latest')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (empty($this->request->post['source_type'])) {
			$this->error['source_type'] = $this->language->get('error_source_type');
		}

		if ($this->request->post['source_type'] == 'category') {
			if (empty($this->request->post['blog_category'])) {
				$this->error['blog_category'] = $this->language->get('error_category');
			}
		}

		return !$this->error;
	}
}

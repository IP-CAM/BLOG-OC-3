<?php
class ControllerXlmBlogBlog extends Controller {
	public function index() {
		$this->load->language('xlm_blog/blog');

		$this->load->model('xlm_blog/blog');
		$this->load->model('tool/image');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['blog_id'])) {
			$blog_id = (int)$this->request->get['blog_id'];
		} else {
			$blog_id = 0;
		}

		$blog_info = $this->model_xlm_blog_blog->getBlog($blog_id);

		if ($blog_info) {
			$this->document->setTitle($blog_info['meta_title']);
			$this->document->setDescription($blog_info['meta_description']);
			$this->document->setKeywords($blog_info['meta_keyword']);

			$this->document->addScript('catalog/view/javascript/xlm_blog/blog.js');
			$this->document->addStyle('catalog/view/javascript/xlm_blog/blog.css');

			$data['breadcrumbs'][] = array(
				'text' => $blog_info['name'],
				'href' => $this->url->link('blog/blog', 'blog_id=' .  $blog_id)
			);

			$data['heading_title'] = $blog_info['name'];
			$data['text_related'] = $this->language->get('text_related');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['description'] = html_entity_decode($blog_info['description'], ENT_QUOTES, 'UTF-8');

			if ($blog_info['cover']) {
				$data['cover'] = $this->model_tool_image->resize($blog_info['cover'], 1366, 768);
			} else {
				$data['cover'] = '';
			}

			if ($blog_info['image']) {
				$data['image'] = $this->model_tool_image->resize($blog_info['image'], 800,450);
			} else {
				$data['image'] = '';
			}

			$data['blogs'] = array();

			$results = $this->model_xlm_blog_blog->getBlogRelated($this->request->get['blog_id']);

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], 800,450);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', 800,450);
				}



				$data['blogs'][] = array(
					'blog_id'  => $result['blog_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'date'				=> date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 150 ).'..',
					'href'        => $this->url->link('xlm_blog/blog', 'blog_id=' . $result['blog_id'])
				);
			}

			$data['tags'] = array();

			if ($blog_info['tag']) {
				$tags = explode(',', $blog_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag))
					);
				}
			}



			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('xlm_blog/blog', $data));
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('blog/blog', 'blog_id=' . $blog_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function agree() {
		$this->load->model('catalog/blog');

		if (isset($this->request->get['blog_id'])) {
			$blog_id = (int)$this->request->get['blog_id'];
		} else {
			$blog_id = 0;
		}

		$output = '';

		$blog_info = $this->model_xlm_blog_blog->getBlog($blog_id);

		if ($blog_info) {
			$output .= html_entity_decode($blog_info['description'], ENT_QUOTES, 'UTF-8') . "\n";
		}

		$this->response->setOutput($output);
	}
}

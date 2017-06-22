<?php
class ModelXlmBlogBlog extends Model {
	public function getBlog($blog_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "blog b
		LEFT JOIN " . DB_PREFIX . "blog_description bd ON (b.blog_id = bd.blog_id)
		LEFT JOIN " . DB_PREFIX . "blog_to_store b2s ON (b.blog_id = b2s.blog_id)
		WHERE b.blog_id = '" . (int)$blog_id . "'
		AND bd.language_id = '" . (int)$this->config->get('config_language_id') . "'
		AND b2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
		AND b.status = '1'");

		return $query->row;
	}

	public function getBlogs($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "blog b
		LEFT JOIN " . DB_PREFIX . "blog_description bd ON (b.blog_id = bd.blog_id)
		LEFT JOIN " . DB_PREFIX . "blog_to_store b2s ON (b.blog_id = b2s.blog_id)
		WHERE bd.language_id = '" . (int)$this->config->get('config_language_id') . "'
		AND b2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
		AND b.status = '1'";


		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "bd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR bd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "bd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			$sql .= ")";
		}

		$sql .= " GROUP BY b.blog_id";

		$sort_data = array(
			'bd.name',
			'b.sort_order',
			'b.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'bd.name' || $data['sort'] == 'b.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY b.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(bd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(bd.name) ASC";
		}



		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getBlogLayoutId($blog_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_to_layout
		WHERE blog_id = '" . (int)$blog_id . "'
		AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getTotalBlogs($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.blog_id) AS total";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "blog_category_path cp LEFT JOIN " . DB_PREFIX . "blog_to_category p2c ON (cp.blog_category_id = p2c.blog_category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "blog_to_category p2c";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "blog p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "blog_description pd ON (p.blog_id = pd.blog_id)
		LEFT JOIN " . DB_PREFIX . "blog_to_store p2s ON (p.blog_id = p2s.blog_id)
		WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
		AND p.status = '1'
		AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.blog_category_id = '" . (int)$data['filter_category_id'] . "'";
			}
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			$sql .= ")";
		}


		$query = $this->db->query($sql);

		return $query->row['total'];
	}


	public function getBlogRelated($blog_id) {
		$blog_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_related pr LEFT JOIN " . DB_PREFIX . "blog p ON (pr.related_id = p.blog_id) LEFT JOIN " . DB_PREFIX . "blog_to_store p2s ON (p.blog_id = p2s.blog_id) WHERE pr.blog_id = '" . (int)$blog_id . "' AND p.status = '1'  AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		foreach ($query->rows as $result) {
			$blog_data[$result['related_id']] = $this->getBlog($result['related_id']);
		}

		return $blog_data;
	}

}

<?php
	 class Brand
	{
		private $brand_name;
		private $id;

		function __construct($brand_name, $id = null)
		{
			$this->brand_name = $brand_name;
			$this->id = $id;
		}

		function getBrandName()
		{
			return $this->brand_name;
		}

		function setBrandName($new_brand_name)
		{
			$this->brand_name = $new_brand_name;
		}

		function getId()
		{
			return $this->id;
		}

		function save()
		{
			$existing_brands = $GLOBALS['DB']->query("SELECT * FROM brands");
			foreach ($existing_brands as $brand) {
				if ($brand['brand_name'] == $this->getBrandName()) {
					return false;
				}
			}
			$GLOBALS['DB']->exec("INSERT INTO brands (brand_name) VALUES ('{$this->getBrandName()}');");
			$this->id = $GLOBALS['DB']->lastInsertId();
		}

		static function getAll()
		{
			$returned_brands = $GLOBALS['DB']->query("SELECT * FROM brands");
			$brands = array();
			foreach($returned_brands as $brand){
				 $brand_name = $brand['brand_name'];
				 $id = $brand['id'];
				 $new_brand = new Brand($brand_name, $id);
				 array_push($brands, $new_brand);
			}
			return $brands;
		}

		static function deleteAll()
		{
			$GLOBALS['DB']->exec("DELETE FROM brands");
		}

		static function find($id)
		{
			$all_brands = Brand::getAll();
			$found_brand = null;
			foreach($all_brands as $brand) {
				$brand_id = $brand->getId();
				if ($brand_id == $id) {
					$found_brand = $brand;
				}
			}
			return $found_brand;
		}

		function addStore($store)
		{
			$existing_store_brands = $GLOBALS['DB']->query("SELECT * FROM stores_brands");
			foreach ($existing_store_brands as $store_brand) {
				if ($store_brand['store_id'] == $store->getId() and $store_brand['brand_id'] == $this->getId()) {
					return false;
				}
			}
			$GLOBALS['DB']->exec("INSERT INTO stores_brands (brand_id, store_id) VALUES ({$this->getId()}, {$store->getId()}) ;");
		}

		function getStores()
		{
			$query = $GLOBALS['DB']->query("SELECT stores.* FROM brands JOIN stores_brands ON (brands.id = stores_brands.brand_id) JOIN stores ON (stores_brands.store_id = stores.id) WHERE brands.id = {$this->getId()}; ");
			$returned_stores = $query->fetchAll(PDO::FETCH_ASSOC);
			$stores = array();
			foreach($returned_stores as $store){
				$store_name = $store['store_name'];
				$id = $store['id'];
				$new_store = new Store($store_name, $id);
				array_push($stores, $new_store);
			}
			return $stores;
		}

		function delete()
		{
		   $GLOBALS['DB']->exec("DELETE FROM brands WHERE id = {$this->getId()};");
		   $GLOBALS['DB']->exec("DELETE FROM stores_brands WHERE brand_id = {$this->getId()};");
		}

		static function search($search_term)
		{
			$query = $GLOBALS['DB']->query("SELECT * FROM brands WHERE brand_name LIKE '%{$search_term}%'");
			$all_brands = $query->fetchAll(PDO::FETCH_ASSOC);
			$found_brands = array();
			foreach ($all_brands as $brand) {
				$brand_name = $brand['brand_name'];
				$id = $brand['id'];
				$new_brand = new Brand($brand_name, $id);
				array_push($found_brands, $new_brand);
			}
			return $found_brands;
		}
	}
 ?>

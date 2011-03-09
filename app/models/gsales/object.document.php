<?php

/*
 * gsFrontend
 * Copyright (C) 2011 Gedankengut GbR Häuser & Sirin <support@gsales.de>
 * 
 * This file is part of gsFrontend.
 * 
 * gsFrontend is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * gsFrontend is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with gsFrontend. If not, see <http://www.gnu.org/licenses/>.
 */

class GSALES2_OBJECT_DOCUMENT extends GSALES2_OBJECT {
	
	protected $id;
	protected $created;
	protected $customers_id;
	protected $original_filename;
	protected $title;
	protected $description;
	protected $public;
	
	public function __construct($arrAPIResult=''){
		if (is_object($arrAPIResult)){
			foreach ($this as $key => $value) $this->$key = $arrAPIResult->$key;
		}
	}
	
	// getters
	
	public function getId(){
		return $this->id;
	}
	
	public function getCreated(){
		return $this->created;
	}	
	
	public function getCustomerId(){
		return $this->customers_id;
	}
	
	public function getOriginalFilename(){
		return $this->customerno;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function getDescription(){
		return $this->description;
	}
	
	public function getPublic(){
		return $this->public;
	}

}
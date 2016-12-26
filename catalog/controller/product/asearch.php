<?php 
class ControllerProductaSearch extends Controller { 	
	public function index() { 
	  
		if(version_compare(VERSION,'1.5.5','>')) {
		  $this->document->addScript('catalog/view/javascript/jquery/jquery.total-storage.min.js');		  			
		}
		
	 
		$this->language->load('product/asearch');
		$txt_reset_filter= $this->language->get('txt_reset_filter');
		$this->load->model('module/supercategorymenuadvanced');
		$this->load->model('catalog/asearch');
		$this->language->load('module/supercategorymenuadvanced');
		$this->load->model('tool/image'); 
		
		
		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['continue'] = $this->url->link('common/home');
		
	
		//Load filter settings.
		$settings_module=$this->config->get('supercategorymenuadvanced_settings');
		
		if (isset($settings_module['countp'])){
			$this->data['count_products']=$settings_module['countp'];
		}else{
			$this->data['count_products']=1;
		}
		//variable with enable/disable follow.
		if (isset($settings_module['nofollow'])){
			$this->data['nofollow']='rel="nofollow"';
		}else{
			$this->data['nofollow']='';
		}
		//variable with enable/disable tracking.
		if (isset($settings_module['track_google'])){
			$this->data['track_google']=$settings_module['track_google'];
		}else{
			$this->data['track_google']=0;
		}
		//variable with enable/disable ajax.		
		if (isset($settings_module['ajax']['ajax'])){
			$this->data['is_ajax']=$settings_module['ajax']['ajax'];
		}else{
			$this->data['is_ajax']=0;
		}
		
		if (isset($settings_module['category']['asearch'])){
			$this->data['is_categories']=$settings_module['category']['asearch'];
		}else{
			$this->data['is_categories']=0;
		}
	   
	    $url_pr='';
		if (isset($this->request->get['PRICERANGE'])) {
			$url_pr .= '&amp;PRICERANGE=' . $this->request->get['PRICERANGE'];
		}
		
			if (isset($this->request->get['C'])) {
				$url_pr .= '&amp;C=' . $this->request->get['C'];
				$filter_coin=$this->request->get['C'];
			} else{
				
				$filter_coin=isset($this->session->data['currency']) ? $this->session->data['currency'] : $this->config->get('config_currency');
				$url_pr .= '&amp;C=' . $filter_coin;
			}
			
		
		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		} 
		
		
		if(version_compare(VERSION,'1.5.5','>')) {
		    
			if (isset($this->request->get['search'])) {
				$filter_name = $this->request->get['search'];
			} else {
				$filter_name = '';
			} 
		
		}else{
			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			} 
			
		}
		
		if (isset($this->request->get['filter_tag'])) {
			$filter_tag = $this->request->get['filter_tag'];
		} else {
			$filter_tag = '';
		} 
				
		if (isset($this->request->get['filter_description'])) {
			$filter_description = $this->request->get['filter_description'];
		} else {
			$filter_description = '';
		} 
				
		if (isset($this->request->get['filter_category_id'])) {
			$filter_category_id = $this->request->get['filter_category_id'];
		} else {
			$filter_category_id = false;
		} 
		
		if (isset($this->request->get['filter_sub_category'])) {
			$filter_sub_category = $this->request->get['filter_sub_category'];
		} else {
			$filter_sub_category = '';
		} 

		
		//searh url filters
		$url_search='';
				
		if(version_compare(VERSION,'1.5.5','>')) {
			if (isset($this->request->get['search'])) {
				$url_search .= '&amp;search=' . $this->request->get['search'];
			}
		
		}else{
			if (isset($this->request->get['filter_name'])) {
			$url_search .= '&amp;filter_name=' . $this->request->get['filter_name'];
		    }
			
		}
		
		if (isset($this->request->get['filter_tag'])) {
			$url_search .= '&amp;filter_tag=' . $this->request->get['filter_tag'];
		}
				
		if (isset($this->request->get['filter_description'])) {
			$url_search .= '&amp;filter_description=' . $this->request->get['filter_description'];
		}
						
		if (isset($this->request->get['filter_category_id'])) {
			$url_search .= '&amp;filter_category_id=' . $this->request->get['filter_category_id'];
		}
		if (isset($this->request->get['filter_sub_category'])) {
			$url_search .= '&amp;filter_sub_category=' . $this->request->get['filter_sub_category'];
		}
		$url_limits='';
	
		if (isset($this->request->get['sort'])) {
			$url_limits.= '&amp;sort=' . $this->request->get['sort'];
		}	
		if (isset($this->request->get['order'])) {
			$url_limits.= '&amp;order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['limit'])) {
			$url_limits.= '&amp;limit=' . $this->request->get['limit'];
		}
		if (isset($this->request->get['page'])) {
			$url_limits.= '&amp;page=' . $this->request->get['page'];
		}
		
    	if (!isset($this->request->get['path']) or empty($this->request->get['path'])) {
			
			if (isset($this->request->get['filter_category_id'])) {
			
				$this->request->get['path']=$this->request->get['filter_category_id'];
			}else{
				$this->request->get['path']=0;
			}
		}else{
		
			$this->request->get['path']=$this->request->get['path'];
			
			
		}
		//where are we
		
		$filter = false;
		//init filters
		$filter_manufacturers_by_id='';	$filter_manufacturers_by_id_string='';	$filter_attributes_by_name='';
		$filter_attribute_id=''; $filter_options_by_name=''; $filter_option_id=''; $filter_attribute_string='';
		$filter_min_price=''; $filter_max_price='';	$filter_stock_id=''; $filter_by_name='';
		$filter_ids=''; $filter_stock=''; $filter_special=''; $filter_clearance='';	$filter_arrivals=''; $filter_width='';
		$filter_height=''; $filter_length=''; $filter_model='';	$filter_sku='';	$filter_upc='';	$filter_location='';$filter_productinfo_id='';
		$filter_weight='';$filter_options_by_ids='';
		$filter_ean=''; $filter_isbn=''; $filter_mpn=''; $filter_jan='';
		$filter_rating='';
	
		$filter_in_manufacturer_id=false;
		$filter_in_category_id=0;
		$filtros_seleccionados= $url_filter='';
	
		//echo $this->request->get['path'];
		
		if (isset($this->request->get['manufacturer_id'])){
			
			$url_where2go="manufacturer_id=".$this->request->get['manufacturer_id'];
			$this->Manufacturer_breadcrumbs($this->request->get['manufacturer_id'],$url_limits);
	  		$filter_in_manufacturer_id=$this->request->get['manufacturer_id'];
			$what="M";
			//check if we have category
			if (isset($this->request->get['path'])) {
				//$path = '';
				$parts = explode('_', (string)$this->request->get['path']);
				$value=array_pop($parts);
				$filter_in_category_id=$value;
				$category_id = $value;
				
			}else{
				$filter_in_category_id=0;
			}
			
	  
		//if we are in home and select a brand we got to brands options.
		}elseif((isset($this->request->get['path']) && $this->request->get['path']==0)){

		  //if isset filter and is only one filter and is manufacturer we go to manufacturer filtering
		   if (!empty($this->request->get['filter'])){
		 	    $filtros = explode("@@",urldecode($this->request->get['filter']));
				list($part1,$name)=explode("=",$filtros[0]);
				list($dnd,$part2)=explode("_",$part1);
				$filter_parts=explode("-", $part2);  
					$tipo =$filter_parts[0];
					$id=$filter_parts[2];				
				
				if ((count($filtros)==1) && $tipo=="m"){
					$manufacturer_id=$id;
					
					$filter_in_manufacturer_id=$manufacturer_id;
					$filter_in_category_id=0;
				    $this->Manufacturer_breadcrumbs($manufacturer_id,$url_limits) ;
					$what="M";
				}else{
		  
					$filter_in_category_id=0;
					$this->Category_breadcrumbs(0,$url_limits);
				    $what="C";
				}
		  
		  
		   }else{
					
					$filter_in_category_id=0;
			  		$this->Category_breadcrumbs(0,$url_limits);
					$what="C";
				}
			   
		  
		
		}else{ // we are not in manufacturer page
	
	
			if (isset($this->request->get['path'])) {
				$path = '';
				$parts = explode('_', (string)$this->request->get['path']);
				$filter_in_category_id=array_pop($parts);
				$this->Category_breadcrumbs($filter_in_category_id,$url_limits);
				$what="C";
			}else{
				$filter_in_category_id=0;
				$this->Category_breadcrumbs(0,$url_limits); 
				$what="C";
			}
		 }
		
			
	if (isset($this->request->get['manufacturer_id'])){
			
			
			$url_where2go="manufacturer_id=".$this->request->get['manufacturer_id'];
			$url_where2go.=(isset($this->request->get['path'])) ? '&path='.$this->request->get['path'] : "&path=0";
			
			$url_where2go_m=(isset($this->request->get['path'])) ? 'path='.$this->request->get['path'] : "path=0";
			
	   }else{
		    
			$url_where2go_m=$url_where2go=(isset($this->request->get['path'])) ? 'path='.$this->request->get['path'] : "path=0";	
			
	   }
			
			
	  
			 
			 
			
		$this->data['reset_all_filter']="<a onclick='testme();' class=\"filter_del_link link_filter_del smenu {dnd:'".$this->url->link('product/asearch', $url_where2go)."&amp;filter=', ajaxurl:'".$url_where2go."&amp;filter=', gapush:'no'}\" href=\"javascript:void(0)\" nofollow><img src=\"image/supermenu/spacer.gif\" alt=\"".$txt_reset_filter."\" title=\"".$txt_reset_filter."\" class=\"filter_del_nav_img\" /></a>";	
	 
		$this->data['isset_subcategories']=false;
		$no_data_text=$this->data['no_data_text'] 		= $this->language->get('no_data_text');
		
		///////////////////////////////////////////////////////////////////
		
		/* FIRST PART */
		//////////////////////////////////////////////////////////////////
		
		// GET VALUES SELECTED
		
		if (!empty($this->request->get['filter'])){
			$has_filter=$this->request->get['filter'];
			$filter=true;
		}else{
			$has_filter=false;
			$filter=false;
		}
		

		if ($has_filter){
		
			$filter=true;
			
		//BEGIN CHECKING FILTERS
		$filtros_seleccionados= array();
			
		$filtros = explode("@@",$has_filter);
		
			foreach ($filtros as $filtro){
				//fix filter string for href[first_position-Second position - only this filter]
				$arr=array();
				$arr = array_diff($filtros,(array)$filtro);
				$links=implode ("@@",$arr);
				list($part1,$name)=explode("=",$filtro);
				list($dnd,$part2)=explode("_",$part1);
						
				$filter_parts=explode("-", $part2);  
					$tipo =$filter_parts[0];
					$special=$filter_parts[1];
					$id=$filter_parts[2];
						
				if (!empty($filter_parts[3])){
					$option_value_id=$filter_parts[3];
				}
						
				if ($special=="i"){
					$image=$this->model_module_supercategorymenuadvanced->getoptionImage($id,$name);
				}else{
					$image='';
				}
						
				$namefinal=html_entity_decode($name)=="NDDDDDN" ? html_entity_decode($no_data_text): html_entity_decode($name);
				        
				if($links){
				    $urlfilter='&amp;filter='.urlencode(str_replace("&amp;","&",$links));
					$hav_filter=1;
				}else{
				    $urlfilter='&amp;filter=';
					$hav_filter=0;
				}		
						
				$see_more_url="index.php?route=module/supercategorymenuadvancedseemore/seemore&amp;path=".$this->request->get['path'].$url_pr.$url_limits.$url_search."&amp;id=".$id."&amp;dnd=".urlencode($dnd)."&amp;tipo=".urlencode($tipo)."&amp;name=".urlencode($namefinal).$urlfilter;
						
				switch($tipo) {
					case 'a': //attribute
						$filter_options_by_ids.="0,";
						switch ($special){
							case 's':
							$filter_attributes_by_name.=$name."sATTNNATT";		
							$filter_attribute_id.=$id.",";	
							$filter_by_name.=$this->model_module_supercategorymenuadvanced->CleanName($name)."sATTNNATT@@@";
							$filter_ids.=$id.",";
							break;
							case 'p':
							$filter_attributes_by_name.=$name."pATTNNATT";		
							$filter_attribute_id.=$id.",";	
							$filter_by_name.=$this->model_module_supercategorymenuadvanced->CleanName($name)."pATTNNATT@@@";
							$filter_ids.=$id.",";
							break;
							case 'n':
							$filter_attributes_by_name.=$name."nATTNNATT";		
							$filter_attribute_id.=$id.",";	
							$filter_by_name.=$this->model_module_supercategorymenuadvanced->CleanName($name)."nATTNNATT@@@";
							$filter_ids.=$id.",";
							break;
						 }
					break;
					case 'm':
						$filter_manufacturers_by_id.=$id.",";
					break;	
			   		case 'o':
						$filter_options_by_name.=$name."OPTTOP";		
						$filter_option_id.=$id.",";	
						$filter_by_name.=$this->model_module_supercategorymenuadvanced->CleanName($name)."OPTTOP@@@";
						$filter_ids.=$id.",";
						$filter_options_by_ids.=$option_value_id.",";
					break;	
					case 'sc':
						$filter_clearance=true;
						$namefinal=$this->language->get('clearance_text');
						$dnd=$this->language->get('stock_text');
						$see_more_url=false;
						break;	
					case 'sn':
						$filter_arrivals=true;
						$namefinal=$this->language->get('new_products_text');
						$dnd=$this->language->get('stock_text');
						$see_more_url=false;
					break;		
					case 'ss':
						$filter_stock=true;
						$dnd=$this->language->get('stock_text');
						$see_more_url=false;
						$namefinal=$this->language->get('in_stock_text');
					break;		
					case 'sp':
						$filter_special=true;
						$dnd=$this->language->get('stock_text');
						$namefinal= $this->language->get('special_prices_text');
						$see_more_url=false;
					break;	
					case 'ra':
						$filter_rating=(int)$name;
						$dnd=$this->language->get('rating_text');
						$namefinal= (int)$name;
						
					break;	
					case 'w':
						switch ($special){
							case 's':
							$filter_width=$name;
							$filter_productinfo_id=$id.",";
							break;
							case 'n':
							$filter_width=$name;
							$filter_productinfo_id=$id.",";
							break;
							}
						break;	
					case 'h':
						switch ($special){
							case 's':
								$filter_height=$name;
								$filter_productinfo_id.=$id.",";
							break;
							case 'n':
								$filter_height=$name;
								$filter_productinfo_id.=$id.",";
								break;
							}
							break;								
					case 'l':
								switch ($special){
									case 's':
									$filter_length=$name;
									$filter_productinfo_id.=$id.",";
									break;
									case 'n':
									$filter_length=$name;
									$filter_productinfo_id.=$id.",";
									break;
									}
								break;									
					case 'sk':
								switch ($special){
									case 's':
									$filter_sku=$name;
									$filter_productinfo_id.=$id.",";
									break;
									case 'n':
									$filter_sku=$name;
									$filter_productinfo_id.=$id.",";
									break;
									}
								break;								
					case 'up':
								switch ($special){
									case 's':
									$filter_upc=$name;
									$filter_productinfo_id.=$id.",";
									break;
									case 'n':
									$filter_upc=$name;
									$filter_productinfo_id.=$id.",";
									break;
									}
								break;	

					case 'lo':
								switch ($special){
									case 's':
									$filter_location=$name;
									$filter_productinfo_id.=$id.",";
									break;
									case 'n':
									$filter_location=$name;
									$filter_productinfo_id.=$id.",";
									break;
									}
								break;
					case 'mo':
								switch ($special){
									case 's':
									$filter_model=$name;
									$filter_productinfo_id.=$id.",";
									break;
									case 'n':
									$filter_model=$name;
									$filter_productinfo_id.=$id.",";
									break;
									}
								break;
					case 'wg':
								switch ($special){
									case 's':
									$filter_weight=$name;
									$filter_productinfo_id.=$id.",";
									break;
									case 'n':
									$filter_weight=$name;
									$filter_productinfo_id.=$id.",";
									break;
									}
								break;	
							
							case 'e':
								switch ($special){
									case 's':
									$filter_ean=$name;
									$filter_productinfo_id.=$id.",";
									break;
									case 'n':
									$filter_ean=$name;
									$filter_productinfo_id.=$id.",";
									break;
									}
								break;	
							
						   case 'i':
								switch ($special){
									case 's':
									$filter_isbn=$name;
									$filter_productinfo_id.=$id.",";
									break;
									case 'n':
									$filter_isbn=$name;
									$filter_productinfo_id.=$id.",";
									break;
									}
								break;	
							case 'p':
								switch ($special){
									case 's':
									$filter_mpn=$name;
									$filter_productinfo_id.=$id.",";
									break;
									case 'n':
									$filter_mpn=$name;
									$filter_productinfo_id.=$id.",";
									break;
									}
								break;	
		
							case 'j':
								switch ($special){
									case 's':
									$filter_jan=$name;
									$filter_productinfo_id.=$id.",";
									break;
									case 'n':
									$filter_jan=$name;
									$filter_productinfo_id.=$id.",";
									break;
									}
								break;	
													
							}
					
					//i . image	//n . normal //s . slider //t . select
					
					//echo $url_where2go;
					
					if (isset($filter_manufacturers_by_id)){
						$href=$this->model_module_supercategorymenuadvanced->SeoFix($this->url->link('product/asearch', $url_where2go_m).$url_pr.$url_limits.$url_search.$urlfilter);
						$ajax_url=$url_where2go_m.$url_pr.$url_limits.$url_search.$urlfilter;
					
					
					}else{
						$href=$this->model_module_supercategorymenuadvanced->SeoFix($this->url->link('product/asearch', $url_where2go).$url_pr.$url_limits.$url_search.$urlfilter);
						$ajax_url=$url_where2go.$url_pr.$url_limits.$url_search.$urlfilter;
					}
					
					//$href=$this->model_module_supercategorymenuadvanced->SeoFix($this->url->link('product/asearch', $url_where2go).$url_pr.$url_limits.$url_search.$urlfilter);
					
					//format product info weight, length, width, height
					
					if ($tipo=="w" || $tipo=="l" || $tipo=="h"){
          
               				$val=$this->length->format($namefinal, $this->config->get('config_length_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
          
            		}elseif($tipo=="wg" ) { 
          
               				$val=$this->weight->format($namefinal, $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
          
         		   }else{
				
							$val=$namefinal;
				
					}
					
					
									    						
					$filtros_seleccionados[utf8_strtoupper($dnd."_".$tipo."_".$id)]=array(
						'id'		   => $id,
						'Tipo' 		   => $tipo,
						'name'		   => html_entity_decode($val),
						'href'		   => $href,
						'ajax_url'	   => $ajax_url,
						'see_more'	   => $see_more_url,
						'dnd'		   => $dnd,
						'image'		   => $image,
						);	
					
								
				unset($filtro);	
							
			}//end foreach $filtros
				
				
			
		
			//EXTRA FILTER OUTSIDE filter
	
		//mount urlfilter for the rest of the filter
		 if ($has_filter){
			
			$min_urls = explode("@@",str_replace("&amp;","&",$has_filter));
			$url_filter=array();
			foreach ($min_urls as $min_url){
				
				
				list($part1,$name)=explode("=",$min_url);
				list($dnd,$part2)=explode("_",$part1);
				$filter_parts=explode("-", $part2);  
					$tipo=$filter_parts[0];
					$special=$filter_parts[1];
					$id=$filter_parts[2];
						
				if (!empty($filter_parts[3])){
					$option_value_id=$filter_parts[3];
				    $url_filter[]=urlencode($dnd)."_".urlencode($tipo)."-".$special."-".$id."-".$option_value_id."=".urlencode($name);
				
				}else{
					$url_filter[]=urlencode($dnd)."_".urlencode($tipo)."-".$special."-".$id."=".urlencode($name);
					
				}
				
				//$url_filter[]=urlencode($tipo) ."=". urlencode($name) ."=". urlencode($id) ."=". urlencode($dnd);
				//list($tipo,$name,$id,$dnd)=explode("=",$min_url);
				
				
			}
				$url_filter="&amp;filter=".implode("@@",$url_filter);
			
			}else{
				$url_filter='&amp;filter=';	
			}	
				
			
		
				
		   
		  } // Mount selected 




//SEARCH BOX
		
        if ($filter_name){// show search box

			if (!empty($this->request->get['filter'])){
				//$url_filter=$this->request->get['filter'];
				//$urlfilter2='&filter='.$this->request->get['filter'];
			     $urlfilter2='&amp;filter='.urlencode(str_replace("&amp;","&",$this->request->get['filter']));
			     $filter_url=$this->model_module_supercategorymenuadvanced->SeoFix($this->url->link('product/asearch', $url_where2go).$url_pr.$url_limits.$urlfilter2);
				 $ajax_url=$url_where2go.$url_pr.$url_limits.$urlfilter2;
			
			}else{
				//$url_filter='';	
				$urlfilter2='&amp;filter=';
				$filter_url=$this->model_module_supercategorymenuadvanced->SeoFix($this->url->link('product/asearch', $url_where2go).$url_pr.$url_limits.$urlfilter2);
				$ajax_url=$url_where2go.$url_pr.$url_limits.$urlfilter2;
				
			}
			//remove filter_name from string.
            
				
			
			
			$filtros_seleccionados[utf8_strtoupper($this->language->get('search_filter_text')."_search_1")]=array( 
			    	'id'		   => "SEARCH",
					'Tipo' 		   => "Search",
					'name'		   => html_entity_decode($filter_name),
					'href'		   => $filter_url,
					'ajax_url'	   => $ajax_url,					
					'see_more'	   => false,
					'dnd'		   => $this->language->get('search_filter_text'),
					'image'		   => '',
			        'tip_div'	   => '',
					'tip_code'	   => '',
			       
			);

        }


	 //PRICE RANGE
		if (isset($this->request->get['PRICERANGE']) and !empty($this->request->get['PRICERANGE'])) {
			
			if (!empty($this->request->get['filter'])){
			     $urlfilter2='&amp;filter='.urlencode(str_replace("&amp;","&",$this->request->get['filter']));
			     $filter_url=$this->model_module_supercategorymenuadvanced->SeoFix($this->url->link('product/asearch', $url_where2go).$url_limits.$urlfilter2);
				 $ajax_url=$url_where2go.$url_limits.$urlfilter2;
			
			}else{
				$urlfilter2='&amp;filter=';
				$filter_url=$this->model_module_supercategorymenuadvanced->SeoFix($this->url->link('product/asearch', $url_where2go).$url_limits.$urlfilter2);
				$ajax_url=$url_where2go.$url_limits.$urlfilter2;
			}
			    $SymbolLeft=$this->currency->getSymbolLeft();
				$SymbolRight=$this->currency->getSymbolRight();
				
				list($filter_min_price,$filter_max_price)=explode(";",$this->request->get['PRICERANGE']);
				list($min_price,$max_price)=explode(";",$this->request->get['PRICERANGE']);
				//remove currency from price
				$filter_min_price=floor($this->model_module_supercategorymenuadvanced->UnformatMoney($filter_min_price,$filter_coin)); 
				$filter_max_price=ceil($this->model_module_supercategorymenuadvanced->UnformatMoney($filter_max_price,$filter_coin));
				
				 if ($this->config->get('config_tax') && $settings_module['pricerange']['setvat']) {
					    $tax_value= $this->tax->calculate(1, $settings_module['pricerange']['tax_class_id'], $this->config->get('config_tax'));
						$filter_min_price=floor( $filter_min_price/$tax_value ); 
						$filter_max_price=ceil( $filter_max_price/$tax_value );
				 }
				
				$txt_price_rage_selected=$SymbolLeft.$min_price.$SymbolRight." - ".$SymbolLeft.$max_price.$SymbolRight;
				
			
			    $filtros_seleccionados[utf8_strtoupper("PR_PRICERANGE_1")]=array(
							'Tipo' 		   => "PRICERANGE",
							'href'		   => $filter_url,
							'ajax_url'	   => $ajax_url,	
							'dnd'		   => $this->language->get('pricerange_text'),
							'name'		   => $txt_price_rage_selected,
						
				);
			
				
			
			} //END PRICE RANG	
			

		//set all values selected.
		$this->data['values_selected']=$filtros_seleccionados;
	    // REMOVE FILTERS CHECKED ON MENU
	    if (!$settings_module['asearch_filters']){ 
			$this->data['values_selected']='';
		}
	 	
		if (isset($this->request->get['keyword'])) {
			$this->document->setTitle($this->data['heading_title'] .  ' - ' . $this->request->get['keyword']);
		} else {
		    $this->document->setTitle($this->data['heading_title']);
		}
		
		$this->data['text_empty'] = $this->language->get('text_empty');
		$this->data['text_category'] = $this->language->get('text_category');
		$this->data['text_sub_category'] = $this->language->get('text_sub_category');
		$this->data['text_quantity'] = $this->language->get('text_quantity');
		$this->data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$this->data['text_model'] = $this->language->get('text_model');
		$this->data['text_price'] = $this->language->get('text_price');
		$this->data['text_tax'] = $this->language->get('text_tax');
		$this->data['text_points'] = $this->language->get('text_points');
		$this->data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
		$this->data['text_display'] = $this->language->get('text_display');
		$this->data['text_list'] = $this->language->get('text_list');
		$this->data['text_grid'] = $this->language->get('text_grid');		
		$this->data['text_sort'] = $this->language->get('text_sort');
		$this->data['text_limit'] = $this->language->get('text_limit');
		$this->data['text_refine'] = $this->language->get('text_refine');
    	$this->data['button_cart'] = $this->language->get('button_cart');
		$this->data['button_wishlist'] = $this->language->get('button_wishlist');
		$this->data['button_compare'] = $this->language->get('button_compare');
		$this->data['compare'] = $this->url->link('product/compare');
		$this->data['remove_filter_text'] = $this->language->get('remove_filter_text');
		$this->data['entry_selected'] = $this->language->get('entry_selected');

		

		$data_filter = array(
				
				'filter_manufacturers_by_id'=> ($filter_in_manufacturer_id) ? $filter_in_manufacturer_id."," : $filter_manufacturers_by_id,  
				'filter_category_id'    	=> ($filter_category_id) ? $filter_category_id:$filter_in_category_id,  
				'filter_min_price'  		=> $filter_min_price,
				'filter_max_price'  	 	=> $filter_max_price, 
				'filter_stock_id'    	    => $filter_stock_id, 
				'filter_by_name' 			=> substr($filter_by_name,0,-3),
				'filter_ids'				=> substr($filter_ids,0,-1),
				'filter_name'         		=> $filter_name, 
				'filter_tag'          		=> $filter_tag, 
				'filter_description'  		=> $filter_description,
				'filter_sub_category' 		=> $filter_sub_category, 
				'filter_stock'				=> $filter_stock,
				'filter_special'			=> $filter_special, 
				'filter_clearance'			=> $filter_clearance,
				'filter_arrivals'			=> $filter_arrivals,
				'filter_width' 				=> $filter_width,
				'filter_height'				=> $filter_height,
				'filter_length' 			=> $filter_length,
				'filter_model' 				=> $filter_model,
				'filter_sku' 				=> $filter_sku,
				'filter_upc' 				=> $filter_upc,
				'filter_location'			=> $filter_location,
				'filter_option_id' 			=> $filter_option_id,
				'filter_attribute_id'		=> $filter_attribute_id,				
				'filter_productinfo_id'		=> $filter_productinfo_id,
				'filter_options_by_ids'		=> $filter_options_by_ids,
				'filter_weight'				=> $filter_weight,
				'filter_ean'				=> $filter_ean,				
				'filter_isbn'				=> $filter_isbn,
				'filter_mpn'				=> $filter_mpn,
				'filter_jan'				=> $filter_jan,				
			    'filter_rating'				=> $filter_rating,			
			);
		

	

		//List of products filtered
		$productos_filtrados= $this->model_module_supercategorymenuadvanced->getProductsFiltered($data_filter,$settings_module['stock']['clearance_value'],$settings_module['stock']['number_day'],$settings_module['stock']['number_day'],$settings_module['reviews']['tipo'],$what);
			
			

			
			
			
		$this->data['products'] = array();
		if (empty($productos_filtrados)){//prevent errors for no products
			$min_price=$maxprice=0;
			$product_total=0;
		    $this->data['ocscroll']='';
		    $this->data['categories']='';
			$this->data['totales'] = 0;
		
		}else{
			
			
		if ($this->data['is_categories']){ // NO CATEGORY NAV IN 
				
			$this->data['categories'] = array();
			
			$results = $this->model_module_supercategorymenuadvanced->getCategoriesFiltered($productos_filtrados,$data_filter);
			foreach ($results as $result) {
				
				
				
			if ($result['image']) {

				$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
				
			} else {
				$image = '';
			}
				
										
				$this->data['categories'][] = array(
					'name'  => $result['name'],
					'href'  => $this->model_module_supercategorymenuadvanced->SeoFix($this->url->link('product/asearch', 'path=' . $this->request->get['path'] . '_' . $result['category_id']) .$url_pr.$url_search),
					'ajax_url'  => 'path=' . $this->request->get['path'] . '_' . $result['category_id'] .$url_pr.$url_search,
					'total'	=> $result['total'],
					'image' =>$image,
					'thumb' =>$image,
				);
			}
			
									
		}else{
			
			$this->data['categories'] = '';
		}
		
		$url = '';
			
			if (isset($this->request->get['filter'])) {
				$url .= $url_filter;
			}
			
			
			if (isset($this->request->get['C'])) {
				$url .= '&amp;C=' . $this->request->get['C'];
		    } 

			if (isset($this->request->get['PRICERANGE'])) {
				$url .= '&amp;PRICERANGE=' . $this->request->get['PRICERANGE'];
		    }			
	
					
			if (isset($this->request->get['limit'])) {
				$url .= '&amp;limit=' . $this->request->get['limit'];
			}
			
			if(version_compare(VERSION,'1.5.5','>')) {
				if (isset($this->request->get['search'])) {
					$url .= '&amp;search=' . $this->request->get['search'];
				}
		
			}else{
				if (isset($this->request->get['filter_name'])) {
				$url .= '&amp;filter_name=' . $this->request->get['filter_name'];
				}
			
			}
		
			if (isset($this->request->get['filter_tag'])) {
				$url .= '&amp;filter_tag=' . $this->request->get['filter_tag'];
			}
				
			if (isset($this->request->get['filter_description'])) {
				$url .= '&amp;filter_description=' . $this->request->get['filter_description'];
			}
				
			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&amp;filter_category_id=' . $this->request->get['filter_category_id'];
			}
		
			if (isset($this->request->get['filter_sub_category'])) {
				$url .= '&amp;filter_sub_category=' . $this->request->get['filter_sub_category'];
			}	
						
	    //for pagination.	
        $product_total = count($productos_filtrados);


		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} 
		else {
			$sort = 'p.special';
		} 

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
  		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
				
		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_catalog_limit');
		}

		//echo $sort; die;

		    $pagination_filters = array(
				'products'					=> implode(",",$productos_filtrados),
				'sort'                		=> $sort,
				'order'               		=> $order,
				'start'               		=> ($page - 1) * $limit,
				'limit'               		=> $limit,
				'filter_category_id'		=> ($filter_category_id) ? $filter_category_id:$filter_in_category_id,
				'filter_sub_category'		=> false
		     );
			
			
			
			
			//print_r('<pre>');
			//print_r( $pagination_filters); die;
			
			
			$results= $this->model_catalog_asearch->getProducts($pagination_filters);
			
			//echo $results; die;

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				} else {
					$image = false;
				}
				
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}
				
				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$special = false;
				}	
				
				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price']);
				} else {
					$tax = false;
				}				
				
				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}
				
				foreach ($this->model_catalog_asearch->getProductOptions($result['product_id']) as $option) { 
				if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') { 
					$option_value_data = array();
					
					foreach ($option['option_value'] as $option_value) {
						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
							if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
								$priceval = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
							} else {
								$priceval = false;
							}
							
							$option_value_data[] = array(
								'product_option_value_id' => $option_value['product_option_value_id'],
								'option_value_id'         => $option_value['option_value_id'],
								'name'                    => $option_value['name'],
								'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
								'price'                   => $priceval,
								'price_prefix'            => $option_value['price_prefix']
							);
						}
					}
					
					$this->data['options'][] = array(
						'product_option_id' => $option['product_option_id'],
						'option_id'         => $option['option_id'],
						'name'              => $option['name'],
						'type'              => $option['type'],
						'option_value'      => $option_value_data,
						'required'          => $option['required']
					);					
				} elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
					$this->data['options'][] = array(
						'product_option_id' => $option['product_option_id'],
						'option_id'         => $option['option_id'],
						'name'              => $option['name'],
						'type'              => $option['type'],
						'option_value'      => $option['option_value'],
						'required'          => $option['required']
					);						
				}
			}
			
				$sizecount=0;
				
				//print_r('<pre>'); print_r($this->data['options']); die;
				foreach($this->data['options'] as $productoptions){
					$optionname=$productoptions[name];
					if($productoptions[name]=='Size')
					{
						$sizecount=count($productoptions[option_value]);

					} 
				}
					
					

				$this->data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $result['rating'],
					'reviews'     => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
					'href'        => $this->url->link('product/product', $url_where2go . '&product_id=' . $result['product_id']). $this->model_module_supercategorymenuadvanced->SeoFix($url),
					'stock_status' =>$result['stock_status'],
					'quantity' =>$result['quantity'],
					'options'	=>$sizecount,
				);

				
			}
					
			
			//print_r('<pre>'); print_r($this->data['products']); die;

			$this->data['sorts'] = array();
			
			/*$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/asearch', $url_where2go . '&sort=p.sort_order&order=ASC') . $this->model_module_supercategorymenuadvanced->SeoFix($url)
			);*/
			
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('product/asearch', $url_where2go . '&sort=pd.name&order=ASC') . $this->model_module_supercategorymenuadvanced->SeoFix($url)
			); 
	
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('product/asearch', $url_where2go . '&sort=pd.name&order=DESC') . $this->model_module_supercategorymenuadvanced->SeoFix($url)
			);

			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_special_asc'),
				'value' => 'p.special-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.special&order=ASC' . $url)
			);

			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_special_desc'),
				'value' => 'p.special-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.special&order=DESC' . $url)
			);

	
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('product/asearch', $url_where2go . '&sort=p.price&order=ASC') . $this->model_module_supercategorymenuadvanced->SeoFix($url)
			); 
	
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('product/asearch', $url_where2go . '&sort=p.price&order=DESC') . $this->model_module_supercategorymenuadvanced->SeoFix($url)
			); 
			
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_desc'),
				'value' => 'rating-DESC',
				'href'  => $this->url->link('product/asearch', $url_where2go . '&sort=rating&order=DESC') . $this->model_module_supercategorymenuadvanced->SeoFix($url)
			); 
			
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_asc'),
				'value' => 'rating-ASC',
				'href'  => $this->url->link('product/asearch', $url_where2go . '&sort=rating&order=ASC') . $this->model_module_supercategorymenuadvanced->SeoFix($url)
			);
			
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('product/asearch', $url_where2go . '&sort=p.model&order=ASC') . $this->model_module_supercategorymenuadvanced->SeoFix($url)
			); 
	
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('product/asearch', $url_where2go . '&sort=p.model&order=DESC') . $this->model_module_supercategorymenuadvanced->SeoFix($url)
			);
			$url = '';
			if (isset($this->request->get['filter'])) {
					$url .= '&amp;filter=' . $url_filter;
			}
            if (isset($this->request->get['C'])) {
			$url .= '&amp;C=' . $this->request->get['C'];
			}
			
			if (isset($this->request->get['PRICERANGE'])) {
				$url .= '&amp;PRICERANGE=' . $this->request->get['PRICERANGE'];
		    }			
				
			if (isset($this->request->get['sort'])) {
				$url .= '&amp;sort=' . $this->request->get['sort'];
			}	
	
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			if(version_compare(VERSION,'1.5.5','>')) {
				if (isset($this->request->get['search'])) {
					$url .= '&amp;search=' . $this->request->get['search'];
				}
		
			}else{
				if (isset($this->request->get['filter_name'])) {
				$url .= '&amp;filter_name=' . $this->request->get['filter_name'];
				}
			
			}
		
			if (isset($this->request->get['filter_tag'])) {
				$url .= '&amp;filter_tag=' . $this->request->get['filter_tag'];
			}
				
			if (isset($this->request->get['filter_description'])) {
				$url .= '&amp;filter_description=' . $this->request->get['filter_description'];
			}
				
			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&amp;filter_category_id=' . $this->request->get['filter_category_id'];
			}
		
			if (isset($this->request->get['filter_sub_category'])) {
				$url .= '&amp;filter_sub_category=' . $this->request->get['filter_sub_category'];
			}	
		
				
			$this->data['limits'] = array();
	
			$limits = array_unique(array($this->config->get('config_catalog_limit'), 25, 50, 75, 100));
			
			sort($limits);
	
			foreach($limits as $limits){
				$this->data['limits'][] = array(
					'text'  => $limits,
					'value' => $limits,
					'href'  => $this->url->link('product/asearch', $url_where2go.'&limit=' . $limits). $url 
				);
			}
				
							
			$url = '';
	
			if (isset($this->request->get['filter'])) {
				$url .= '&amp;filter=' . $url_filter;
			}
			
			if (isset($this->request->get['C'])) {
				$url .= '&amp;C=' . $this->request->get['C'];
		    }			
			if (isset($this->request->get['PRICERANGE'])) {
				$url .= '&amp;PRICERANGE=' . $this->request->get['PRICERANGE'];
		    }			
			

										
			if (isset($this->request->get['sort'])) {
				$url .= '&amp;sort=' . $this->request->get['sort'];
			}	
	
			if (isset($this->request->get['order'])) {
				$url .= '&amp;order=' . $this->request->get['order'];
			}
			
			if (isset($this->request->get['limit'])) {
				$url .= '&amp;limit=' . $this->request->get['limit'];
			}
			if(version_compare(VERSION,'1.5.5','>')) {
				if (isset($this->request->get['search'])) {
					$url .= '&amp;search=' . $this->request->get['search'];
				}
		
			}else{
				if (isset($this->request->get['filter_name'])) {
				$url .= '&amp;filter_name=' . $this->request->get['filter_name'];
				}
			
			}
		
			if (isset($this->request->get['filter_tag'])) {
				$url .= '&amp;filter_tag=' . $this->request->get['filter_tag'];
			}
				
			if (isset($this->request->get['filter_description'])) {
				$url .= '&amp;filter_description=' . $this->request->get['filter_description'];
			}
				
			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&amp;filter_category_id=' . $this->request->get['filter_category_id'];
			}
		
			if (isset($this->request->get['filter_sub_category'])) {
				$url .= '&amp;filter_sub_category=' . $this->request->get['filter_sub_category'];
			}	
			//print_r('<pre>');
			//print_r($this->data['products']); die;
			//echo $sort .' '.$order; die;
					
			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->text = $this->language->get('text_pagination');
			$pagination->url = $this->url->link('product/asearch',$url_where2go.'&page={page}') . $url;
			
			$this->data['pagination'] = $pagination->render();
			$this->data['totales'] =  $product_total;
			//Load filter settings.
			$this->data['ocscroll']='';
			$settings_module=$this->config->get('supercategorymenuadvanced_settings');
			if($settings_module['ocscroll']){
				$this->load->model('module/ocscroll');
				$this->data['ocscroll']=$this->model_module_ocscroll->setocScroll();
			}
		
		//}	
		
		$this->data['filter'] = $filter;
		//$this->data['PRICERANGE'] = $filter_price;
		$this->data['path']=$filter_in_category_id;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['limit'] = $limit;
		
		}//end for no products
		
		if($this->data['is_ajax'] && isset($this->request->get['a'])){
			
			$this->children = array(
			'common/content_filter',
			'common/column_left',
			'common/column_right',
			);	
			
			$what_template="asearch_a.tpl";
		}else{
			
			$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_filter',
			'common/content_bottom',
			'common/footer',
			'common/header'
			);
			$what_template="asearch.tpl";
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/'.$what_template)) {
			$this->template = $this->config->get('config_template') . '/template/product/'.$what_template;
		} else {
			$this->template = 'default/template/product/'.$what_template;
		}
		
				
		$this->response->setOutput($this->render());
  	}


	function Manufacturer_breadcrumbs($id,$url_limits){
		
		$this->load->model('catalog/manufacturer');
			//set right filter
			
			
			$url_seo="manufacturer_id=".$id;
					   
		   //then check if we are in manufacturer page and we have select a category
		    if (isset($this->request->get['path'])){
	        $path = '';
			$parts = explode('_', (string)$this->request->get['path']);
			$filter_in_category_id = array_pop($parts);
	        }
	   
	   
	   		//creating the right breadcrumbs
	   		$this->data['breadcrumbs'] = array();

	   		$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
        	'separator' => false
      			);
		
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_brand'),
				'href'      => $this->url->link('product/manufacturer'),
				'separator' => $this->language->get('text_separator')
			);
	   
	   
	   	$title_name=false;
	   	$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($id);
	
		if ($manufacturer_info) {
	   		$title_name=$manufacturer_info['name'];
			$this->data['breadcrumbs'][] = array(
       			'text'      => $manufacturer_info['name'],
				'href'      => $this->url->link('product/asearch', $url_seo . $url_limits),
      			'separator' => $this->language->get('text_separator')

   			);
	   
	   
		}
	   
	    if (!$title_name){
    		$this->data['heading_title'] = sprintf($this->language->get('heading_title'),$this->language->get('this_store'));
		}else{
			$this->data['heading_title'] = sprintf($this->language->get('heading_title'),$title_name);
		}
		
	}
	
	
	function Category_breadcrumbs($id,$url_limits){
	   
		$this->load->model('catalog/category');
		$url_seo="path=".$id;
	   	
		$this->data['breadcrumbs'] = array();
   		$this->data['breadcrumbs'][] = array( 
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
      		'separator' => false
   		);
		
		$path = '';
		$parts = explode('_', (string)$this->request->get['path']);
		foreach ($parts as $path_id) {
		  if (!$path) {
			$path = $path_id;
		  } else {
			$path .= '_' . $path_id;
		  }
		$title_name=false;   
		$category_info = $this->model_catalog_category->getCategory($path_id);
				
			if ($category_info) {
			 	$title_name=$category_info['name'];
	       		$this->data['breadcrumbs'][] = array(
   	    			'text'      => $category_info['name'],
						'href'      => $this->url->link('product/asearch', 'path=' . $path. $url_limits),
        			'separator' => $this->language->get('text_separator')
        		);
			}
			
		}		
		
		if (!$title_name){
    	$this->data['heading_title'] = sprintf($this->language->get('heading_title'),$this->language->get('this_store'));
		}else{
		$this->data['heading_title'] = sprintf($this->language->get('heading_title'),$title_name);
		}
	
		
	}
	

	
}
?>
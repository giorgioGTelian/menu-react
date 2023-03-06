<?php 
    function print_menu_shortcode($atts, $content = null) {
extract(shortcode_atts(array( 'name' => null, 'class' => null ), $atts));
return wp_nav_menu( array( 'menu' => $name, 'menu_class' => $class, 'echo' => false ) );
}

add_shortcode('menu_custom', 'print_menu_shortcode');
//shortcode articoli 3 evidenza 
//uso : Header home
add_shortcode('stickyslider', 'stickypostslider');
    function stickypostslider($attrs) {
        $return = '';
		$elemento ='';
    extract(  shortcode_atts( array(
        'taxonomy' => ''//default param
        ), $attrs ) );
 $lang = pll_current_language();
		if($lang=='it'){$term_id='3745';}
				if($lang=='en'){$term_id='3793';}
    $args = array(
		'post_type' => 'post',
		'lang'=>$lang,
		 'post_status' => 'publish',
         //'post__in' => get_option( 'sticky_posts' ),
           'posts_per_page' => 3,
		 'orderby' => 'date',
    'order'   => 'DESC',
		   /* 'meta_query' => array( array(
									'key' => 'evidenza',
									'value' =>'evidenza',
									'compare' => 'LIKE',
								) ),*/
		     'tax_query' => array(
            array(
                'taxonomy' => 'hm_post_categoria_speciale',
                'field' => 'term_id',
                'terms' => $term_id,
            )
        )
  );
		$count = 0;
$hmel_query = new WP_Query($args);
        if ($hmel_query->have_posts()):
            while($hmel_query->have_posts()):
            $hmel_query->the_post();
		$count++;
            $id= get_the_ID();
            $title = get_the_title();
            $excerpt = get_the_excerpt( $id );
    $excerpt =  wp_trim_words( get_the_content(), 35, '' ).'...';
		if($count<4){
            $return .= '<div class="articolo_slider" id="'.$count.'">';      
            $return .= '<div class="post_info"><h5 class="post_title" style="color:#fff !important"><a href="'.get_permalink().'" alt="'.$title.'" style="color:#fff">'.$title.'</a></h5>
           <br>   </div>';
                  //  <a class="et_pb_button et_pb_button_1 et_pb_bg_layout_light" href="'.get_permalink().'" alt='.$title.'>'.pll__('Approfondisci').'</a>
                 

            $return .= '</div>';
		}
              endwhile;
        else:

endif;
        wp_reset_query();
    wp_reset_postdata();
    return  $return;
}
//carosello home 

//shortcode carosello eventi

//shortcode custom post type stagione carosello 
function ajax_r(){

    //global $wpdb;  
    global $wp_query;
$cat_post = $_GET['categoria'];
$tassonomia = $_GET['tassonomia'];
$numeropost=$_GET['numero_post'];
$home = $_GET['home'];
$tipo=$_GET['tipo'];
$id_slider = $_GET['id_slider'];
$count='';
$return = '';
	$elemento='';
$order_by = 'rand';
		$order='DESC';
if($tipo == 'hm_post'){$tipo='post';
	$order_by = 'date';
	$order='DESC';
                      }
	 $lang = pll_current_language();
	$lang = $_GET['lingua'];
$hmargs_loop = array(
        'post_type' => $tipo,
	'lang'=>$lang,
       'post_status' => 'publish',
        'posts_per_page' => '9',

    'tax_query' => array(
        array(
                    'taxonomy' => $tassonomia,
                    'field' => 'slug',
                    'terms' => $cat_post,
        ),
    ),
    );
	if($tipo=='hm_evento'){
			/*$hmargs_loop['meta_query'] = array(
			array(
			 'key' => 'data_inizio',
            'value' => date("Y-m-d"),
            'type' => 'DATE',
            'compare' => '>',
			),
		);*/
		    $hmargs_loop['meta_key'] = 'data_inizio';
		$hmargs_loop['meta_type'] = 'DATETIME';
  $hmargs_loop['orderby'] = 'meta_value';
		$hmargs_loop['order'] = 'ASC';
		
				$term_slug='evento-home';
		/*if($lang=='it'){$term_slug='evento-home';}
				if($lang=='en'){$term_slug='evento-home-en';}*/
		$hmargs_loop['tax_query'][] = array(			
			'taxonomy' => 'tag_evento',
			'field' => 'slug',
			'terms' => $term_slug,			
		);
		 $hmargs_loop['tax_query']['relation'] = 'AND';
	}
		
 if(($tipo=='hm_experience')AND($lang=='it')AND($home=='is_home')) {
	  //echo $home;

		$term_slug='exp-home';
		/*if($lang=='it'){$term_slug='exp_home';}
				if($lang=='en'){$term_slug='exp-home-en';}*/
		$hmargs_loop['tax_query'][] = array(			
			'taxonomy' => 'tag',
			'field' => 'slug',
			'terms' => $term_slug,			
		);
		 $hmargs_loop['tax_query']['relation'] = 'AND';
	}

	//INTEGRAZIONE INGLESE
	/*if($tipo=='hm_experience') {
		$lang = pll_current_language();
		if($lang=='it'){$term_slug='exp_home';}
				if($lang=='en'){$term_slug='exp_home_en';}
		$hmargs_loop['tax_query'][] = array(			
			'taxonomy' => 'tag',
			'field' => 'slug',
			'terms' => $term_slug,			
		);
		 $hmargs_loop['tax_query']['relation'] = 'AND';
	}*/
/*echo '<pre>';
	var_dump($hmargs_loop);
	echo '</pre>';*/
$hmel_query = new WP_Query($hmargs_loop);
        if ($hmel_query->have_posts()):
            $return = '<div id="'.$id_slider.'" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">';
           
            while($hmel_query->have_posts()):
            $hmel_query->the_post();
           $view ='show';
	$data_inizio ='';
	$data_fine ='';
           
            $id= get_the_ID();
	if($tipo=='hm_evento'){
		$view = 'not_show';
		$data_inizio = get_post_meta($id, 'data_inizio', true );
			$data_fine = get_post_meta($id, 'data_fine', true );
		$oggi = date("Y-m-d");
		if(($data_inizio==$data_fine)AND($data_inizio>$oggi)){
			$view = 'show';			
		}
		if(($data_inizio!=$data_fine)AND($data_fine>=$oggi)){
			$view = 'show';			
		}
		//echo $id.''.get_the_title().''.$view.' iniz'.$data_inizio.' fine'.$data_fine.'<br>';
	}
	if($view!='not_show'){
		 $count++;
            $title = get_the_title();
if($count==1){
     $return .= '<div class="carousel-item active">
        <div class="row">';
}
if($count==4){
     $return .= '<div class="carousel-item">
        <div class="row">';
}
if($count==7){
     $return .= '<div class="carousel-item">
        <div class="row">';
}
		$imagine  = get_the_post_thumbnail_url($id,'medium');
    if ($tipo=='hm_evento'){
		$elemento = get_post_meta($id, 'data', true );
						   if($elemento != ''){
					$elemento = '<span class="underline">'.get_post_meta($id, 'data', true ).'</span>';
							   
						   }
							else {
								$elemento = get_post_meta($id, 'data_inizio', true );
										   $elemento = date("d/m/Y", strtotime($elemento));
						   $mesi = array(1=>'Gennaio', 'Febbraio', 'Marzo', 'Aprile',
               'Maggio', 'Giugno', 'Luglio', 'Agosto',
               'Settembre', 'Ottobre', 'Novembre','Dicembre');
						   $mese = explode('/',$elemento);
		$mese_date = (int)$mese[1];
						$elemento = '<span class="underline">'.$mese[0].' '.$mesi[$mese_date].' '.$mese[2].'</span>';
							}
			$lang = pll_current_language();
	if($lang=='en'){	$elemento = get_post_meta($id, 'data_inizio', true );
										   $elemento = date("d/m/Y", strtotime($elemento));
						   $mesi = array(1=>'Gennaio', 'Febbraio', 'Marzo', 'Aprile',
               'Maggio', 'Giugno', 'Luglio', 'Agosto',
               'Settembre', 'Ottobre', 'Novembre','Dicembre');
						   $mese = explode('/',$elemento);
		$mese_date = (int)$mese[1];
						$elemento = '<span class="underline">'.$mese[0].' '.$mesi[$mese_date].' '.$mese[2].'</span>';}
						   }
        if ($tipo=='post'){
			$elemento = get_the_time('Y-m-d', $id);
			if($elemento!=''){
			$mesi = array(1=>'Gennaio', 'Febbraio', 'Marzo', 'Aprile',
               'Maggio', 'Giugno', 'Luglio', 'Agosto',
               'Settembre', 'Ottobre', 'Novembre','Dicembre');
						   $mese = explode('-',$elemento);
		$mese_date = (int)$mese[1];
						$elemento ='<span class="underline">'. $mese[2].' '.$mesi[$mese_date].' '.$mese[0].'</span>';}
						  }

        if($tipo=='hm_itinerario'){
			$elemento = get_post_meta($id, 'lunghezza', true );
			if($elemento!=''){ $elemento = '<span class="underline">'.get_post_meta($id, 'lunghezza', true ).'</span>';}}
       if($tipo=='hm_experience'){
		   $elemento='';
		   $prezzo='';
			$prezzo = wp_get_post_terms( $id, 'hm_experience_prezzo',array( 'fields' => 'all' ));

		   if( ! empty( $prezzo ) && ! is_wp_error( $prezzo ) ) {
    $taxonomy = $prezzo[0]->name;
			   $elemento='<span class="underline">'.$taxonomy.'</span>';
}

								  }
$return .= '<div class="col-sm-4 box_'.$tipo.' " id="'.$count.'"> <div class="thumb-wrapper">';
$return .= '<div class="img-box"><a href="'.get_permalink($id).'" alt="'.$title.'" title="'.$title.'"><img src="'.$imagine.'" class="img-fluid" alt="'.$title.'" title="'.$title.'"></a></div>';
$return .=' <div class="thumb-content">'.$elemento.'<h4 style="color:#4d4d4d"><a href="'.get_permalink().'" alt="'.$title.'" title="'.$title.'" >'.$title.'</a></h4>
                            </div>';            
$return.='</div></div>';
if($count == 3){
    $return .='</div>';//close row
        $return .='</div>';//close carousel item
}
if($count == 6){
    $return .='</div>';//close row
        $return .='</div>';//close carousel item
}

}
              endwhile;
	
				if($count >=7){
					
    $return .='</div>';//close row
        $return .='</div>';//close carousel item
}
			/*	if($count ==8){
    $return .='</div>';//close row
        $return .='</div>';//close carousel item
}
		if($count ==9){
    $return .='</div>';//close row
        $return .='</div>';//close carousel item
}*/
              $return.='</div>';
	if($count>3){
		
     $return.='<button class="carousel-control-prev" type="button" data-bs-target="#'.$id_slider.'" data-bs-slide="prev">
        <span class="carousel-control-prev-icon '.$count.'" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#'.$id_slider.'" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>';}
$return.='</div>';
	
        else:
	$return ='<span class="no_post_messaggio">';
$return .= pll__('Purtroppo non abbiamo trovato contenuti pertinenti.<br> Prova ad ampliare i tuoi criteri di ricerca oppure contattaci per una proposta personalizzata.');
	$return .='</span>';
endif;
if($id_slider=='vivi'){
	if($count>3){	
	//ITALIANO
	$lang = pll_current_language();
	if($lang=='it'){
		if ($tipo=='hm_evento'){$link_all = '/liguria-eventi/';
							   $label = 'Tutti gli eventi';}
        if ($tipo=='post'){$link_all = '/la-liguria-racconta/';
							   $label =  'Tutti i consigli';}
        if($tipo=='hm_itinerario'){$link_all = '/itinerari-in-liguria/';
							   $label = 'Tutti gli itinerari';}
        if($tipo=='hm_experience'){$link_all = '/experience-liguria/';
							   $label = 'Tutte le Experience';}
	}else{
		//INGLESE
		if ($tipo=='hm_evento'){$link_all = '/events-in-liguria/';
							   $label = 'All events';}
        if ($tipo=='post'){$link_all = '/liguria-tells-a-story/';
							   $label =  'All tips';}
        if($tipo=='hm_itinerario'){$link_all = '/itineraries-in-liguria/';
							   $label = 'All itineraries';}
        if($tipo=='hm_experience'){$link_all = '/experience-in-liguria/';
							   $label = 'All Experience';}
	}
		 
	$return .='	<div class="lc-block text-center mt-3"><a class="btn btn-primary" href="'.$link_all.'" role="button">'.$label.'</a></div>';}}
        wp_reset_query();
    wp_reset_postdata();
    
    echo $return;
exit();
}
add_action ( 'wp_ajax_myshortcode', 'ajax_r' );
add_action ( 'wp_ajax_nopriv_myshortcode', 'ajax_r' );

function viviall(){

    //global $wpdb;  
    global $wp_query;
$numeropost=$_GET['numero_post'];
$tipo=$_GET['tipo'];
$id_slider = $_GET['id_slider'];
	
$count='';
$return = '';
	$elemento='';
if($tipo == 'hm_post'){$tipo='post';
                      }
	 $lang = pll_current_language();
$hmargs_loop = array(
        'post_type' => $tipo,
	'lang'=>$lang,
       'post_status' => 'publish',
        'posts_per_page' => $numeropost,
    );

$hmel_query = new WP_Query($hmargs_loop);
        if ($hmel_query->have_posts()):
            $return = '<div id="'.$id_slider.'" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">';
            while($hmel_query->have_posts()):
            $hmel_query->the_post();
            $count++;
            $id= get_the_ID();
            $title = get_the_title();
if($count==1){
     $return .= '<div class="carousel-item active">
        <div class="row">';
}
if($count==4){
     $return .= '<div class="carousel-item">
        <div class="row">';
}
if($count==7){
     $return .= '<div class="carousel-item">
        <div class="row">';
}
    if ($tipo=='hm_evento'){$elemento = get_post_meta($id, 'data_inizio', true );
						   if($elemento != ''){
							   $elemento = date("d/m/Y", strtotime($elemento));
						   $mesi = array(1=>'Gennaio', 'Febbraio', 'Marzo', 'Aprile',
               'Maggio', 'Giugno', 'Luglio', 'Agosto',
               'Settembre', 'Ottobre', 'Novembre','Dicembre');
						   $mese = explode('/',$elemento);
		$mese_date = (int)$mese[1];
						$elemento = '<span class="underline">'.$mese[0].' '.$mesi[$mese_date].' '.$mese[2].'</span>';
						   }
							else{$elemento = '<span class="underline">'.get_post_meta($id, 'data', true ).'</span>';}
						   }
        if ($tipo=='post'){$elemento = get_the_time('Y-m-d', $id);
						  		$mesi = array(1=>'Gennaio', 'Febbraio', 'Marzo', 'Aprile',
               'Maggio', 'Giugno', 'Luglio', 'Agosto',
               'Settembre', 'Ottobre', 'Novembre','Dicembre');
						   $mese = explode('-',$elemento);
		$mese_date = (int)$mese[1];
						   if($elemento!=''){$elemento = '<span class="underline">'.$mese[2].' '.$mesi[$mese_date].' '.$mese[0].'</span>';}
						  }
        if($tipo=='hm_itinerario'){$elemento = get_post_meta($id, 'lunghezza', true );
								     if($elemento!=''){$elemento = '<span class="underline">'.$elemento.'</span>';}}
       if($tipo=='hm_experience'){
		   $elemento='';
		   $prezzo='';
			$prezzo = wp_get_post_terms( $id, 'hm_experience_prezzo',array( 'fields' => 'all' ));

		   if( ! empty( $prezzo ) && ! is_wp_error( $prezzo ) ) {
    $taxonomy = $prezzo[0]->name;
			   $elemento='<span class="underline">'.$taxonomy.'</span>';
}

								  }
	$imagine  = get_the_post_thumbnail_url($id,'medium');
$return .= '<div class="col-sm-4 box_'.$tipo.'" id="'.$count.'"> <div class="thumb-wrapper">';
$return .= '<div class="img-box"><a href="'.get_permalink($id).'" alt="'.$title.'" title="'.$title.'"><img src="'.$imagine.'" class="img-fluid" alt="'.$title.'"></a></div>';
$return .=' <div class="thumb-content">'.$elemento.'<h4 style="color:#4d4d4d" ><a href="'.get_permalink($id).'" alt="'.$title.'" title="'.$title.'">'.$title.'</a></h4>

                            </div>';            
$return.='</div></div>';
if($count <= 3){
    $return .='</div>';//close row
        $return .='</div> xx';//close carousel item
}
if($count == 6){
    $return .='</div>';//close row
        $return .='</div>';//close carousel item
}
if($count ==9){
    $return .='</div>';//close row
        $return .='</div>';//close carousel item
}
              endwhile;
	 	 
echo $count.'xxxx';
              $return.='</div>
    <button class="carousel-control-prev  xxxx" type="button" data-bs-target="#'.$id_slider.'" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#'.$id_slider.'" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>';
        else:
	$return ='<span class="no_post_messaggio">';
$return .= pll__('Purtroppo non abbiamo trovato contenuti pertinenti.<br> Prova ad ampliare i tuoi criteri di ricerca oppure contattaci per una proposta personalizzata.');
	$return .='</span>';
endif;

		if ($tipo=='hm_evento'){$link_all = '/vivi';
							   $label = 'Tutti gli eventi';}
        if ($tipo=='post'){$link_all = '/vivi';
							   $label = 'Tutti i consigli';}
        if($tipo=='hm_itinerario'){$link_all = '/vivi';
							   $label = 'Tutti gli itinerari';}
        if($tipo=='hm_experience'){$link_all = '/vivi';
							   $label = 'Tutte le Experience';}
	$return .='	<div class="lc-block text-center mt-3"><a class="btn btn-primary" href="'.$link_all.'" role="button">'.$label.'</a></div>';
        wp_reset_query();
    wp_reset_postdata();
    
    echo $return;
exit();
}
add_action ( 'wp_ajax_myshortcode_vivi_all', 'viviall' );
add_action ( 'wp_ajax_nopriv_myshortcode_vivi_all', 'viviall' );

//Shortcode custompost type no slider
add_shortcode('custompost_visual', 'custompost_visual_function');
 function custompost_visual_function($attrs){
$count='';
$return = '';
	 $elemento ='';
	 $sticky_posts='';
        global $wpdb;
    extract(  shortcode_atts( array(
        'tipo' => 'tipo', 
        'numeropost' => 'numeropost',
        'categoria' => 'categoria',
        'tassonomia' =>'tassonomia', 
		'sticky_posts' => 'sticky_posts',
		'order'=>'order',
		'valore'=>'valore',
		'visualizzazione'=>'visualizzazione',
		'not_in'=>'not_in',
        ), $attrs ) );
if($tipo == 'hm_post'){$tipo='post';
                      }

	 if($sticky_posts=='sticky_posts'){  
		  $lang = pll_current_language();
		 $hmargs_loop = array(
			  'post__not_in' => array($not_in),
        'post_type' => $tipo,
        'post_status' => 'publish',
		'lang'=>$lang,
        'orderby' => 'rand',
        'posts_per_page' => $numeropost,
		    'meta_query' => array( array(
									'key' => 'evidenza',
									'value' =>'evidenza',
									'compare' => 'LIKE',
								) )
    );
	 }
	 
else {		 
	 $lang = pll_current_language();
	$hmargs_loop = array(
		 'post__not_in' => array($not_in), 
        'post_type' => $tipo,
		'lang'=>$lang,
        'post_status' => 'publish',
        'orderby' => 'rand',
        'posts_per_page' => $numeropost,
    );}
	 if($order=='ordina'){
		 	 $lang = pll_current_language();
         $hmargs_loop = array(
        'post_type' => $tipo,
			  'post__not_in' => array($not_in),
        'post_status' => 'publish',
			 'lang'=>$lang,
         'posts_per_page' => $numeropost,
        'orderby'        => 'meta_value',       // Or post by custom field
        'meta_key'       => $valore,
    );
	 }
	 if ($tipo=='hm_evento'){
		 $hmargs_loop = array(
        'post_type' => $tipo,
			 // 'post__not_in' => array($not_in),
        'post_status' => 'publish',
			 'lang'=>$lang,
         'posts_per_page' => $numeropost,
		 	'orderby'        => 'meta_value', 
  	'order'            => 'ASC',
    'meta_key'       => 'data_inizio',
    'meta_type'        => 'DATETIME', 
    /*'meta_query' => array(
        array(
            'key' => 'data_inizio',
            'value' => date("Y-m-d"),
            'type' => 'DATE',
            'compare' => '>',
        )
    )*/);	 if($sticky_posts=='sticky_posts'){
						 $hmargs_loop = array(
        'post_type' => $tipo,
			 // 'post__not_in' => array($not_in),
        'post_status' => 'publish',
			 'lang'=>$lang,
         'posts_per_page' => $numeropost,
		 	'orderby'        => 'meta_value', 
  	'order'            => 'ASC',
    'meta_key'       => 'data_inizio',
    'meta_type'        => 'DATETIME', 
	'meta_query' => 
 array(
 'key' => 'evidenza',
 'value' => 'evidenza',
 'compare' => 'LIKE',
 ));

	 }
	 }
	 
if ($categoria!='no_theme') { 
	if(strpos($categoria, '/') !== false){
$categoria=explode('/',$categoria);
$tassonomia=explode('/',$tassonomia);
$termine1 = $categoria[0];
$termine2= $categoria[1];
$tassonomia1=$tassonomia[0];
$tassonomia2=$tassonomia[1];
            $hmargs_loop['tax_query']=array(
        'relation' => 'AND',
         array(
                    'taxonomy' => $tassonomia1,
                    'field' => 'slug',
                    'terms' => $termine1,
                ),
        array(
                     'taxonomy' => $tassonomia2,
                    'field' => 'slug',
                    'terms' => $termine2,
                ),
);
} else{       $hmargs_loop['tax_query'] = array(
                array(
                     'taxonomy' => $tassonomia,
                    'field' => 'slug',
                    'terms' => $categoria,
                ),
            );
    }
}
$tot_row = 12/$numeropost;

if($numeropost=='-1'){
	$return = '<div class="row">';
	$tot_row = 3;
}
/*echo '<pre>';
	 var_dump($hmargs_loop);
	 echo '</pre>';*/
$hmel_query = new WP_Query($hmargs_loop);

        if ($hmel_query->have_posts()):

     $return .='<div class=" ">';
            while($hmel_query->have_posts()):
            $hmel_query->the_post();
                    // $id= get_the_ID();
	 $view ='show';
	$data_inizio ='';
	$data_fine ='';
        
            $id= get_the_ID();
	if($tipo=='hm_evento'){
		$view = 'not_show';
		$data_inizio = get_post_meta($id, 'data_inizio', true );
			$data_fine = get_post_meta($id, 'data_fine', true );
		$oggi = date("Y-m-d");
		if(($data_inizio==$data_fine)AND($data_inizio>$oggi)){
			$view = 'show';			
		}
		if(($data_inizio!=$data_fine)AND($data_fine>=$oggi)){
			$view = 'show';			
		}
		//echo $id.''.get_the_title().''.$view.' iniz'.$data_inizio.' fine'.$data_fine.'<br>';
	}
	if($view!='not_show'){
		$count++;
            $title = get_the_title();


//$data= get_the_date($id);   
        
    if ($tipo=='hm_evento'){$elemento = get_post_meta($id, 'data_inizio', true );
						   if($elemento != ''){
							   $elemento = date("d/m/Y", strtotime($elemento));
	$mesi = array(1=>'Gennaio', 'Febbraio', 'Marzo', 'Aprile',
               'Maggio', 'Giugno', 'Luglio', 'Agosto',
               'Settembre', 'Ottobre', 'Novembre','Dicembre');
						   $mese = explode('/',$elemento);
		$mese_date = (int)$mese[1];
						$elemento = $mese[0].' '.$mesi[$mese_date].' '.$mese[2];
							   if($elemento!=''){$elemento = '<span class="underline">'.$elemento.'</span>';}
						   }
							else{$elemento = '<span class="underline">'.get_post_meta($id, 'data', true ).'</span>';}
						   }
        if ($tipo=='post'){
						   //$elemento =  get_the_date( 'F Y', $id_post);
			$elemento='';
						   if($elemento!=''){$elemento = '<span class="underline">'.$elemento.'</span>';}
						  }
        if($tipo=='hm_itinerario'){$elemento = get_post_meta($id, 'lunghezza', true );
								  if($elemento!=''){$elemento = '<span class="underline">'.$elemento.'</span>';}}
              if($tipo=='hm_experience'){
		   $elemento='';
		   $prezzo='';
			$prezzo = wp_get_post_terms( $id, 'hm_experience_prezzo',array( 'fields' => 'all' ));

		   if( ! empty( $prezzo ) && ! is_wp_error( $prezzo ) ) {
    $taxonomy = $prezzo[0]->name;
			   $elemento='<span class="underline">'.$taxonomy.'</span>';
}

								  }
$imagine  = get_the_post_thumbnail_url($id,'full');		
if($visualizzazione=='cards'){	

		 $return .= '<div id="'.$count.'" class="col-md-'.$tot_row.' shadow-lg custom_post box_'.$tipo.'"><div class="thumb-wrapper">';
		$return .= '<div class="img-box"><a href="'.get_permalink($id).'" alt="'.$title.'" title="'.$title.'"><img src="'.$imagine.'" class="img-fluid" alt="'.$title.'" title="'.$title.'"></a></div>';
		$return .=' <div class="thumb-content">'.$elemento.'<h4 style="color:#4d4d4d"><a href="'.get_permalink($id).'" alt="'.$title.'" title="'.$title.'">'.$title.'</a></h4></div>';            
		$return.='</div></div>';
		if($numeropost=='-1'){
		if($count % 4 == 0){
		$return.= '</div><div class="row" style="margin-top:2%">';
		}

	}
}          
	 if($visualizzazione=='cards_menu'){	
		 $return .= '<div id="'.$count.'" class="col-md-'.$tot_row.' shadow-lg custom_post box_'.$tipo.'"><div class="thumb-wrapper">';
		$return .= '<div class="img-box"><a href="'.get_permalink($id).'" alt="'.$title.'" title="'.$title.'"><img src="'.$imagine.'" class="img-fluid" alt="'.$title.'"></a></div>';
		$return .=' <div class="thumb-content">
		<h4 style="color:#4d4d4d"><a href="'.get_permalink($id).'" alt="'.$title.'" title="'.$title.'">'.$title.'</a></h4></div>'.$elemento;            
		$return.='</div></div>';
		if($numeropost=='-1'){
		if($count % 4 != 0){
		$return.='</div><div class="row">';
		}

	}
}
	 	 if($visualizzazione=='cards_ev'){	
		 $return .= '<div id="'.$count.'" class="col-md-6 custom_post box_'.$tipo.' cards_ev" style="height:auto">
		 <div class="contenuto_evento col-md-12" >  
		 <div class="thumb-wrapper col-md-3" style="float:left">';
		$return .= '<div class="img-box" ><a href="'.get_permalink($id).'" alt="'.$title.'" title="'.$title.'"><img src="'.$imagine.'" class="img-fluid" alt="'.$title.'"></a></div></div>';
		$return .=' <div class="thumb-content col-md-9" style="float:right">
		<h4 style="color:#4d4d4d"><a href="'.get_permalink($id).'" alt="'.$title.'" title="'.$title.'">'.$title.'</a></h4>'.$elemento;     
		$return.='</div></div></div>';
		/*if($numeropost=='-1'){
		if($count % 4 != 0){
		$return.='</div><div class="row">';
		}

	}*/
}
	if($visualizzazione=='lista'){
		 $return .= '<div class="col-md-5 custom_post list">
		 <div class="thumb-wrapper col-md-12">';
		$return .= '<div class="img-box col-md-4"><img src="'.$imagine.'" class="img-fluid" alt="'.$title.'"></div>';
		$return .=' <div class="thumb-content col-md-8">'.$elemento.'
		<h4 style="color:#4d4d4d">	<a href="'.get_permalink($id).'" alt="'.$title.'">'.$title.'</a></h4>
		<a href="'.get_permalink($id).'" title="leggi consiglio"  role="button" class="stretched-link">'.pll__('Approfondisci').'</a>
								</div>';            
		$return.='</div></div>';
				if($numeropost=='-1'){
		if($count % 4 != 0){
		$return.='</div><div class="row">';
		}

	}
	} 
	 
	 if($visualizzazione=='lig_wow'){
		 $return .='
		 <div class="col-md-'.$tot_row.' shadow-lg custom_post box_'.$tipo.'">
		 	<div class="lc-block single-item">	
				<img class="img-fluid shadow" src="'.$imagine.'" alt="'.$title.'">
			<div class="contenuto_wow">	<h4 class="wow_title">'.$title.'</h4><a href="'.get_permalink($id).'" title="leggi consiglio"  role="button" class="btn btn-primary">'.pll__('Leggi').'</a></div>
						</div></div>';
	 }
	}
	endwhile;
	if($numeropost=='-1'){
	$return .='</div></div>';
}
	 else{}
        else:
	$return ='<span class="no_post_messaggio">';
$return .= pll__('Purtroppo non abbiamo trovato contenuti pertinenti.<br> Prova ad ampliare i tuoi criteri di ricerca oppure contattaci per una proposta personalizzata.');
	$return .='</span>';
endif;
        wp_reset_query();
    wp_reset_postdata();
    return  $return;
 }


// multi nuovo 
 add_shortcode('caroselloeventi', 'carosello_Slider');
 function carosello_Slider($attrs){
	 	 $lang = pll_current_language();
	$count=0;
$return = '';
	     extract(shortcode_atts(array(
        'tassonomia' => 'tassonomia',
		 'valore'=>'valore'), $attrs)); 
	 $elemento='';
        global $wpdb;
$hmargs_loop = array(
        'post_type' => 'hm_evento',
        'post_status' => 'publish',
        'posts_per_page' => '8',
         'lang'             => $lang,
			 	'orderby'        => 'meta_value', 
  	'order'            => 'ASC',
    'meta_key'       => 'data_inizio',
    'meta_type'        => 'DATETIME', 
   /* 'meta_query' => array(
        array(
            'key' => 'data_inizio',
            'value' => date("Y-m-d"),
            'type' => 'DATE',
            'compare' => '>',
        )
    )*/
    );
  if($tassonomia!='') {
	  $hmargs_loop['tax_query']=array(
        'relation' => 'AND',
         array(
                    'taxonomy' => $tassonomia,
                    'field' => 'slug',
                    'terms' => $valore,
                ),
	  );
		  }
$hmel_query = new WP_Query($hmargs_loop);
       if ($hmel_query->have_posts()):
			$return .='<div id="sliderEventi" class="multi-carousel carousel slide" data-bs-ride="carousel">
				<div class="carousel-inner" role="listbox">';
		while($hmel_query->have_posts()):
            $hmel_query->the_post();	
        
            $id= get_the_ID();
	 		$view = 'not_show';
		$data_inizio = get_post_meta($id, 'data_inizio', true );
			$data_fine = get_post_meta($id, 'data_fine', true );
		$oggi = date("Y-m-d");
		if(($data_inizio==$data_fine)AND($data_inizio>$oggi)){
			$view = 'show';			
		}
		if(($data_inizio!=$data_fine)AND($data_fine>=$oggi)){
			$view = 'show';			
		}
	$elemento = get_post_meta($id, 'data', true );
						   if($elemento != ''){
							   $elemento = '<span class="underline titolo_grigio">'.$elemento.'</span>';
							   /*
							   $elemento = date("d/m/Y", strtotime($elemento));
						   $mesi = array(1=>'Gennaio', 'Febbraio', 'Marzo', 'Aprile',
               'Maggio', 'Giugno', 'Luglio', 'Agosto',
               'Settembre', 'Ottobre', 'Novembre','Dicembre');
						   $mese = explode('/',$elemento);
		$mese_date = (int)$mese[1];
						$elemento = '<span class="underline titolo_grigio">'.$mese[0].' '.$mesi[$mese_date].' '.$mese[2].'</span>';
						   
						   */
						   }
	  $imagine  = get_the_post_thumbnail_url($id,'medium');		
      $title = get_the_title();
	 	if($view!='not_show'){
       $count++;
      if($count==1){ 
        $return .='<div class="carousel-item active">';
       } 
	else{  $return .='<div class="carousel-item ">';}
	// $str = 'There is something to type here. This is an example text';
$title= preg_replace( '~((?:\S*?\s){5})~', "$1\n", $title );
	$return.='<div class="col-md-3">
							<div class="thumb-content" id="'.$id.'"  >
								<div class="img-box"><div class="overlay_Ev"></div><a href="'.get_permalink($id).'" alt="'.$title.'" title="'.$title.'"><img src="'.$imagine.'" title="'.$title.'" alt="'.$title.'" title="'.$title.'"></a></div>
								
							<div class="contenuto"><a href="'.get_permalink($id).'" alt="'.$title.'" style="color:#fff">'.$elemento.'<h4 >'.$title.'</a></h4>
     <a href="'.get_permalink($id).'" title="'.pll__('Approfondisci').'" class="stretlink">'.pll__('Approfondisci').'</a>  </div>
						</div></div>
					</div>';
		}
           endwhile;
				$return .= '</div>
				<a class="carousel-control-prev bg-transparent w-aut " href="#sliderEventi" role="button" data-bs-slide="prev">
					<span class="carousel-control-prev-icon shadow-lg" aria-hidden="true"></span>
				</a>
				<a class="carousel-control-next bg-transparent w-aut" href="#sliderEventi" role="button" data-bs-slide="next">
					<span class="carousel-control-next-icon shadow-lg" aria-hidden="true"></span>
				</a>
			</div>';
 else:
	$return ='<span class="no_post_messaggio">';
$return .= pll__('Purtroppo non abbiamo trovato contenuti pertinenti.<br> Prova ad ampliare i tuoi criteri di ricerca oppure contattaci per una proposta personalizzata.');
	$return .='</span>';
endif;
        wp_reset_query();
    wp_reset_postdata();
    return  $return;
}
//griglia post
function lc_get_posts_mycustom_view($the_posts,$get_posts_shortcode_atts) {
	extract($get_posts_shortcode_atts);
	$out=''; // INIT
	$elemento='';
 $out='<div class="row lp ">';
	foreach ( $the_posts as $the_post ):  
	$id_post = $the_post->ID;
    $title = get_the_title($id_post);
	$out .='<div class="col-md-3 col-sm-6 mb-3 ">';
$out.='<div class="card shadow hm_loop_post">';
$out .='<div class="card-body" >';
	$imagine  = get_the_post_thumbnail_url($id_post,'medium');		
	$tipo = get_post_type($id_post);
	$out.='<div class="img-box"><div class="overlay"></div><a href="'.get_permalink($id_post).'" alt="'.$title.'" title="'.$title.'"><img src="'.$imagine.'"  class="img-fluid w-100 wp-post-image"  alt="'.$title.'" title="'.$title.'"></a></div>';
	
	$out .='<div class="thumb-content">';
		$id=$id_post;
    if ($tipo=='hm_evento'){$elemento = get_post_meta($id, 'data_inizio', true );
						   if($elemento != ''){
							   $elemento = date("d/m/Y", strtotime($elemento));
						   $mesi = array(1=>'Gennaio', 'Febbraio', 'Marzo', 'Aprile',
               'Maggio', 'Giugno', 'Luglio', 'Agosto',
               'Settembre', 'Ottobre', 'Novembre','Dicembre');
						   $mese = explode('/',$elemento);
		$mese_date = (int)$mese[1];
						$elemento = $mese[0].' '.$mesi[$mese_date].' '.$mese[2];}
						   }
        if ($tipo=='post'){
			//$elemento =  get_the_date( 'F Y', $id_post);
			$elemento='';
						  if ($elemento!=''){$elemento ='<span class="underline">'.$elemento.'</span>';}}
        if($tipo=='hm_itinerario'){$elemento = get_post_meta($id_post, 'lunghezza', true );
							 if ($elemento!=''){$elemento ='<span class="underline">'.$elemento.'</span>';}	  }
       if($tipo=='hm_experience'){
		   $elemento='';
		   $prezzo='';
			$prezzo = wp_get_post_terms( $id, 'hm_experience_prezzo',array( 'fields' => 'all' ));

		   if( ! empty( $prezzo ) && ! is_wp_error( $prezzo ) ) {
    $taxonomy = $prezzo[0]->name;
			   $elemento='<span class="underline">'.$taxonomy.'</span>';
}

								  }
	if ($elemento!=''){$out.=$elemento;}
		if($tipo == 'hm_localita'){ 
			$out .='<a  href="'.get_permalink($id).'" title="'.$title.'"><h3  style="text-align:center;padding:2% 0">'.$title.'</h3></a>';
			$sottotitolo = get_post_meta($id, 'sottotitolo', true );
	$out .='<p class="card-text">'.$sottotitolo.'</p>'; 							  
								  }
	else { $out .='<h3 class="titolo_grigio" style="text-align:center;padding:2% 0"><a class=""stretched-link" href="'.get_permalink($id_post).'" title="'.$title.'">'.$title.'</a></h3>'; }
	$out .='<a class="stretched-link" href="'.get_permalink($id).'" title="'.$title.'">'.pll__('Approfondisci').'</a></div>';
	$out .= '</div></div>';
	$out.='</div>';
?>
 
   <?php endforeach;
	$out.='</div>';
   return  $out;
}


 add_shortcode('composit_wow', 'composit_wow_function');
 function composit_wow_function(){
	 
	 $stampa=array();
	 $return ='';
	 $i=0;
	 	 $lang = pll_current_language();
$posts_array = get_posts(
    array(
        'posts_per_page' => '8',
		'orderby' => 'rand',
    'order'    => 'ASC',
        'post_type' => 'post',
		'lang'=>$lang,
        'tax_query' => array(
            array(
                'taxonomy' => 'hm_post_categoria_speciale',
                'field' => 'term_id',
                'terms' => '763',
            )
        )
    )
	);
	  foreach ( $posts_array as $post ) {
		  $imagine  = get_the_post_thumbnail_url($post->ID,'medium');
		  $title =get_the_title($post->ID);
		$return .= '<div id="'.$count.'" class="col-md-'.$tot_row.' shadow-lg custom_post box_post"><div class="thumb-wrapper">';
		$return .= '<div class="img-box"><a href="'.get_permalink($post->ID).'" alt="'.$title.'"><img src="'.$imagine.'" class="img-fluid" alt="'.$title.'" title="'.$title.'"></a></div>';
		$return .=' <div class="thumb-content">'.$elemento.'<h4 style="color:#4d4d4d"><a href="'.get_permalink($post->ID).'" alt="'.$title.'" title="'.$title.'">'.$title.'</a></h4></div>';            
		$return.='</div></div>';
		if($numeropost=='-1'){
		if($count % 4 == 0){
		$return.= '</div><div class="row" style="margin-top:2%">';
		}

	}
		 $stampa[$i]['link'] = get_permalink($post->ID);
		 $stampa[$i]['immagine'] = get_post_meta($post->ID, 'immagine_ev', true );
		 $stampa[$i]['titolo']=get_the_title($post->ID);
		  $i++;
	 }
	 return $return;
 }

function myloadmore(){

    //global $wpdb;  
   // global $wp_query;
	$tassonomia='';
$tassonomia= '"';
	$meta_k='';
	$meta_value='';
	$paesaggio='';
	$durata='';
	$prezzo='';
	$lunghezza='';
	$tema='';
	$territorio='';
	$stagione='';
	$cat_speciale='';
	$localita='';

	$cat_speciale=$_GET['categoria_speciale']; 
	$stagione=$_GET['stagione']; 
	$numero_post_ora = $_GET['numero_post'];
	$paesaggio = $_GET['paesaggio'];
	$tema = $_GET['tema'];
	$lunghezza = $_GET['lunghezza'];
	$territorio = $_GET['territorio'];
	$tipo = $_GET['tipo'];		
	$durata = $_GET['durata'];	
	$prezzo = $_GET['prezzo'];	
	$localita = $_GET['localita'];
	
	if(($territorio=='')OR($territorio=='Seleziona')){
	}
	else{$tassonomia .= $tipo.'_territorio='.$territorio.',';}

	if(($localita=='Seleziona')OR($localita=='')){}
	else{	$tassonomia .= $tipo.'_localita='.$localita.',';}

	if($tipo=='hm_experience') {	
	if(($durata=='Seleziona')OR($durata=='')){}
	else {$tassonomia .= $tipo.'_durata='.$durata.',';	}

	if(($prezzo=='Seleziona')OR($prezzo=='')){}
	else {$tassonomia .= $tipo.'_prezzo='.$prezzo.',';}
	}
	
	if(($stagione=='Seleziona')OR($stagione=='')){}
	else{$tassonomia .= $tipo.'_stagione='.$stagione.',';}

	if(($cat_speciale=='Seleziona')OR($cat_speciale=='')){}
	else{	$tassonomia .= $tipo.'_categoria_speciale='.$cat_speciale.',';}

	if(($tema=='Seleziona')OR($tema=='')){}
	else{$tassonomia .= $tipo.'_tematica='.$tema.',';	}

	if(($paesaggio=='Seleziona')OR($paesaggio=='')){}
	else {$tassonomia .= $tipo.'_paesaggio='.$paesaggio.',';}

	if($tipo=='hm_post'){$tipo='post';}
	if($tipo=='hm_itinerario') {
		if(($lunghezza=='Seleziona')OR($lunghezza=='')){}
		else{
		$meta_k = 'meta_key="lunghezza"';
		$meta_value = 'meta_value="'.$lunghezza.'"';
	}
	}
	$tassonomia .='"';
$return .= '<input type="hidden" name="tipo" id="tipo" value="'.$tipo.'">';
$return .=' <input type="hidden" value="'.$numero_post_ora.'" id="numero_post_now_now">';

	echo do_shortcode('[lc_get_posts post_type="'.$tipo.'" '.$meta_k.' '.$meta_value.' tax_query='.$tassonomia.' output_view="lc_get_posts_mycustom_view" output_number_of_columns="4" posts_per_page="'.$numero_post_ora.'" output_article_class="shadow" output_featured_image_class="card-img-top" ]').$return;
	
	
exit();

}
add_action ( 'wp_ajax_myloadmore', 'myloadmore' );
add_action ( 'wp_ajax_nopriv_myloadmore', 'myloadmore' );
add_shortcode('filter_search_post','filter_search_post_function');
function filter_search_post_function($attrs){
    $localita_short ='';
	$elemento='';
    extract(shortcode_atts(array(
        'tipo' => 'tipo'), $attrs)); 
   
	$territorio = get_terms( $tipo.'_territorio', array(
            'hide_empty' => true,
            ) );
     $paesaggio = get_terms( $tipo.'_paesaggio', array(
            'hide_empty' => true,
        ) );
     $tema = get_terms( $tipo.'_tematica', array(
            'hide_empty' => true,
        ) );
	$localita =  get_terms( $tipo.'_localita', array(
            'hide_empty' => true,
        ) );
	$durata = get_terms( $tipo.'_durata', array(
            'hide_empty' => true,
        ));
	$prezzo = get_terms( $tipo.'_prezzo', array(
            'hide_empty' => true,
        ));
	$stagione = get_terms( $tipo.'_stagione', array(
            'hide_empty' => true,
        ));
	 $cat_speciale = get_terms( $tipo.'_categoria_speciale', array(
            'hide_empty' => true,
        ));

$return ='<div id="ricerca_container">';
$return .= '<input type="hidden" name="tipo" id="tipo" value="'.$tipo.'">';
$return .=' <input type="hidden" value="12" id="numero_post_now">';
	$tipo_count=$tipo;
	if($tipo=='hm_post'){$tipo_count='post';}
	
	$count_posts= wp_count_posts( $post_type = $tipo_count);
	$totale_post = $count_posts->publish;
$return .=' <input type="hidden" value="'.$totale_post.'" id="stop">';
$return .='
<div class="row">';
	 if($tipo=='hm_evento'){  
            global $wpdb;
            $table = $wpdb->base_prefix.'postmeta';
            $data_inizio = $wpdb->get_results( 'SELECT DISTINCT meta_value FROM '.$table.' WHERE meta_key LIKE "data_inizio"', OBJECT );
    		if (is_array($data_inizio)){
			 $return .= '<div class="col-md-2"><select name="lunghezza" id="lunghezza" class="select_ajax">';
				$return .='<option value="Seleziona">Data</option>';
			foreach($data_inizio as $data_inizio){
				$nome_term = $data_inizio->meta_value;
				$id= $data_inizio->term_id;
				   $return.= '<option value="'.$id.'">'.$nome_term.'</option>';
			}
    $return .='</select></div>';
	}
	 }
	 if($tipo=='hm_itinerario'){  
        /*    global $wpdb;
            $table = $wpdb->base_prefix.'postmeta';
            $lunghezza = $wpdb->get_results( 'SELECT DISTINCT meta_value FROM '.$table.' WHERE meta_key LIKE "lunghezza"', OBJECT );
    		if (is_array($lunghezza)){
			 $return .= '<div class="col-md-2"><select name="lunghezza" id="lunghezza" class="select_ajax">';
				$return .='<option value="Seleziona">Lunghezza</option>';
			foreach($lunghezza as $lunghezza){
				$nome_term = $lunghezza->meta_value;
				$id= $lunghezza->term_id;
				   $return.= '<option value="'.$id.'">'.$nome_term.'</option>';
			}
    $return .='</select></div>';
	}*/
	 }

	if (is_array($localita) ){
		if (($tipo=='hm_evento')OR($tipo=='hm_post')OR($tipo=='hm_experience')OR($tipo=='hm_itinerario')){
	 $return .= '<div class="col-md-2"><select name= "'.$tipo.'_localita" id="'.$tipo.'_localita" class="select_ajax">';
        $return .='<option value="Seleziona">'.pll__('Località').'</option>';
    foreach($localita as $localita){
    	$nome_term = $localita->name;
		$id= $localita->term_id;
           $return.= '<option value="'.$id.'">'.$nome_term.'</option>';
    }
    $return .='</select></div>';
	}
	}
		if (is_array($prezzo)){
	 $return .= '<div class="col-md-2"><select name= '.$tipo.'"_prezzo" id="'.$tipo.'_prezzo" class="select_ajax">';
        $return .='<option value="Seleziona">'.pll__('Prezzo').'</option>';
    foreach($prezzo as $prezzo){
    	$nome_term = $prezzo->name;
		$id= $prezzo->term_id;
           $return.= '<option value="'.$id.'">'.$nome_term.'</option>';
    }
    $return .='</select></div>';
	}
    if (is_array($territorio)){
		$lbl_terr = pll__('Territori');
			if (($tipo!='hm_experience')){
	$return .='<div class="col-md-2"><select name="'.$tipo.'_territorio" id="'.$tipo.'_territorio" class="select_ajax">';
		if( $tassonomia==$tipo.'_territorio'){
				
			}
        else {$return .='<option value="Seleziona">'.$lbl_terr.'</option>';}
		 foreach($territorio as $ter){
         $territorio = $ter->name;
		 $id= $ter->term_id;
         $return.= '<option value="'.$id.'">'.$territorio.'</option>';
    }
    $return .='</select></div>';
			}
	}
	if (is_array($paesaggio)){
	if (($tipo=='hm_itinerario')OR($tipo=='hm_post')){
	$return .= '<div class="col-md-2"><select name= "'.$tipo.'_paesaggio" id="'.$tipo.'_paesaggio" class="select_ajax"><option value="Seleziona">'.pll__('Paesaggio').'</option>';
        foreach($paesaggio as $paes){
             $paesaggio = $paes->name;
			$id= $paes->term_id;
             $return.= '<option value="'.$id.'">'.$paesaggio.'</option>';
        }
        $return .='</select></div>';  
    }
	}
	
	if (is_array($tema)){
	 $return .= '<div class="col-md-2"><select name= '.$tipo.'"_tematica" id="'.$tipo.'_tematica" class="select_ajax">';
        $return .='<option value="Seleziona">'.pll__('Tematica').'</option>';
    foreach($tema as $tem){
    	$nome_term = $tem->name;
		$id= $tem->term_id;
           $return.= '<option value="'.$id.'">'.$nome_term.'</option>';
    }
    $return .='</select></div>';
	}
	if (is_array($durata)){
	 $return .= '<div class="col-md-2"><select name= '.$tipo.'"_durata" id="'.$tipo.'_durata" class="select_ajax">';
        $return .='<option value="Seleziona">'.pll__('Durata').'</option>';
    foreach($durata as $durata){
    	$nome_term = $durata->name;
		$id= $durata->term_id;
           $return.= '<option value="'.$id.'">'.$nome_term.'</option>';
    }
    $return .='</select></div>';
	}

	if (is_array($stagione)){
	 $return .= '<div class="col-md-2"><select name= "'.$tipo.'_stagione" id="'.$tipo.'_stagione" class="select_ajax">';
        $return .='<option value="Seleziona">'.pll__('Stagione').'</option>';
    foreach($stagione as $stagione){
    	$nome_term = $stagione->name;
		$id= $stagione->term_id;
           $return.= '<option value="'.$id.'">'.$nome_term.'</option>';
    }
    $return .='</select></div>';
	}

    $return .='<div class="col-md-2"><input name="submit" type="submit" value="'.pll__("Filtra").'" id="invia"></div></div></div>';
    return $return;
}

function mega_menu_result(){
		$id_value = $_GET['id_valore'];
	if($id_value=='all') {
		$valore ='no_theme';
		$tassonomia='';
	}
	else {
		$tassonomia ='hm_experience_tematica';
		$valore=$id_value;
	}

	echo do_shortcode('[custompost_visual tipo="hm_experience" numeropost="3" sticky_posts="" tassonomia="'.$tassonomia.'" categoria="'.$valore.'" visualizzazione="cards_menu" order="" label=""]');
exit();

}
add_action ( 'wp_ajax_mega_menu_result', 'mega_menu_result' );
add_action ( 'wp_ajax_nopriv_mega_menu_result', 'mega_menu_result' );


add_shortcode('localita_meteo', 'localita_meteofunction');
 function localita_meteofunction(){
	 	 $lang = pll_current_language();
	         global $wpdb;
$hmargs_loop = array(
        'post_type' => 'hm_localita',
        'post_status' => 'publish',
	'lang'=>$lang,
        'posts_per_page' => '-1',
		'orderby'=> 'title',
	'order' => 'ASC',
	);

$hmel_query = new WP_Query($hmargs_loop);
       if ($hmel_query->have_posts()):
		while($hmel_query->have_posts()):
            $hmel_query->the_post();	
 
            $id= get_the_ID();
	 $title = get_the_title();
$return .='<div class="col-md-4"><a href="#met_'.$title.'">'.$title.'</a></div>';
$return_second .='<div class="row sezione_template_space" id="met_'.$title.'">
<h3 class="titolo_grigio pt-5">'.$title.'</h3>
<div class="col-md-12  ps-5 mt-3 shadow text-center bg-body pb-5 pt-5">

<div lc-helper="shortcode" class="live-shortcode">[simple-weather location="'.$title.'" days="7" night="no" text_align="right" display="block" style="large-icons"]</div></div>
</div>'; 
           endwhile;
		
 else:
	$return ='<span class="no_post_messaggio">';
$return .= pll__('Purtroppo non abbiamo trovato contenuti pertinenti.<br> Prova ad ampliare i tuoi criteri di ricerca oppure contattaci per una proposta personalizzata.');
	$return .='</span>';
endif;
        wp_reset_query();
    wp_reset_postdata();
	 $return_full= $return.$return_second;
    return  do_shortcode($return_full);
	
}

add_action ( 'wp_ajax_mega_menu_result_scopri', 'mega_menu_result_scopri' );
add_action ( 'wp_ajax_nopriv_mega_menu_result_scopri', 'mega_menu_result_scopri' );
function mega_menu_result_scopri(){
	  global $wpdb;
		 //$lang = pll_current_language();
		$id_value = $_GET['id_territorio'];
$lang = $_GET['lingua'];
$hmargs_loop = array(
        'post_type' => 'hm_localita',
        'post_status' => 'publish',
        'posts_per_page' => '9',
	'lang'=>$lang,
		'orderby'=> 'title',
	'order' => 'ASC',
	);
 $hmargs_loop['tax_query'] = array(
                array(
                     'taxonomy' => 'hm_localita_territorio',
                    'field' => 'slug',
                    'terms' => $id_value,
                ),
            );
/*	echo '<pre>';
	var_dump($hmargs_loop);
	echo '</pre>';*/
$hmel_query = new WP_Query($hmargs_loop);
       if ($hmel_query->have_posts()):
		while($hmel_query->have_posts()):
            $hmel_query->the_post();	
 
            $id= get_the_ID();
	 $title = get_the_title();
	$link = get_the_permalink();
$return .='<div class="col-md-12"><a href="'.$link.'" title="'.$title.'" style="font-size:15px">'.$title.'</a></div>';
	endwhile;
	endif;
	$return .='<div class="lc-block mt-2"><a class="btn btn-primary" href="/territori/'.$id_value.'" role="button">'.pll__('Tutte le località').'</a></div>';
        wp_reset_query();
    wp_reset_postdata();
    
    echo $return;
exit();

}

add_shortcode('list_localita', 'shortcode_list_localita');
function shortcode_list_localita(){
	 $lang = pll_current_language();
	  global $wpdb;
		//$id_value = $_GET['id_territorio'];
$hmargs_loop = array(
        'post_type' => 'hm_localita',
        'post_status' => 'publish',
	'lang'=>$lang,
        'posts_per_page' => '20',
		'orderby'=> 'title',
	'order' => 'ASC',
	);
 /*$hmargs_loop['tax_query'] = array(
                array(
                     'taxonomy' => 'hm_localita_territorio',
                    'field' => 'slug',
                    'terms' => $id_value,
                ),
            );*/
$hmel_query = new WP_Query($hmargs_loop);

       if ($hmel_query->have_posts()):
	
		while($hmel_query->have_posts()):
            $hmel_query->the_post();	
 
            $id = get_the_ID();
	 $title = get_the_title();

	$tips_title = str_replace(" ", "_", $title);


	$link = get_the_permalink();
		
		$return.='<div data-imp-trigger-shape-on-mouseover="tip_'.$tips_title.'" class="my_tips_loc"><a href="'.$link.'">'.$title.'</a></div>';
				
	endwhile;
	endif;

        wp_reset_query();
    wp_reset_postdata();
    
    return $return;

}

function lista_operatori(){
	$args = array(
    'role'    => 'operatore',
    'orderby' => 'user_nicename',
    'order'   => 'ASC'
);
$users = get_users( $args );
$return ='<input type="text" id="myInput" onkeyup="myfilter()" placeholder="Ricerca per nome" title="Type in a name" class="select-styled" style="padding:1%">
<ul id="my_listaoperatori">';
foreach ( $users as $user ) {
	$email='';
	$user_id= $user->ID;
	$email_ref = get_user_meta( $user_id, 'email_referente',true );
		$email = $user->user_email;
		$email_operat = get_user_meta( $user_id, 'email_operatore',true );
		if($email_ref!=''){
			$email .= ','.$email_ref;
		}
		if($email_operat!=''){
			$email .= ','.$email_operat;
		}
    	$ragione_sociale = get_user_meta( $user_id, 'ragione_sociale',true );
		$tipologia_operatore = get_user_meta( $user_id, 'tipologia_operatore',true );
	$return.= '<li  data-tipologia="'.$tipologia_operatore.'">
	<label for="user_'.$user_id.'" class="checkbox-inline container_check_child" >
	<input type="checkbox" value="user_'.$user_id.'" name="'.$val.'" class="check_child active_child" data-email="'.$email.'" onclick="check_operatore()">'.$ragione_sociale.'</label>
	</li>';
}
$return .='</ul>';
echo $return;
	exit();
}
add_action ( 'wp_ajax_lista_operatori', 'lista_operatori' );
add_action ( 'wp_ajax_nopriv_lista_operatori', 'lista_operatori' );
?>
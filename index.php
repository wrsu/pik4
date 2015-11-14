<?php

/*******************************************************************************

 *
 * 	AlterVision Core Framework - Writer Club project
 * 	Created by AlterVision - altervision.me
 *  Copyright © 2005-2015 Anton Reznichenko
 *

 *
 *  File: 			index.php
 *  Description:	Picture hosting core
 *  Author:			Anton 'AlterVision' Reznichenko - altervision13@gmail.com
 *

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

*******************************************************************************/

//
// Full Site Configs
#configurations
error_reporting( 0 );
define( 'PATH', 		dirname(__FILE__) . '/' );
define( 'TMP',			PATH . 'f/%s' );		// Temp File Path
define( 'MAXS', 		3 );		// Maximum Aspect Ratio for Cut Image
define( 'FOLDER',		0777 );		// Folder Permissions
define( 'PFS',			1280 );		// Picture Full Size
define( 'PMS',			640 );		// Picture Medium Size
define( 'PTS',			128 );		// Picture Thumb Size
define( 'PICS',  		PATH . '%s/%s/%s/%s/%s/%s.jpg' );	// Picture Server path
define( 'DOMAIN',		'pik4.work' );						// image domain
define( 'PICV',			'http://'.DOMAIN.'/h%s' ); 			// Picture Viewer path
define( 'SERVER',		'http://'.DOMAIN );					// image host
define( 'UPLOAD',		'http://'.DOMAIN );					// upload host
define( 'PICV',			'http://'.DOMAIN.'/h%s' );			// viewer link
define( 'PICH',			'http://'.DOMAIN.'/%s%s.jpg' ); 	// Picture Host path
define( 'PICF',			'http://'.DOMAIN.'/f%s.jpg' );		// large image link
define( 'PICI',			'http://'.DOMAIN.'/i%s.jpg' );		// medium image link
define( 'PICT',			'http://'.DOMAIN.'k/t%s.jpg' );		// small image link
define( 'VKAPP',		'12345678' );						// Your VK.com application ID
//

//
// Language Messages - edit the right parts only!
function l( $id ) {

	$lang = array(

		// Site Texts
		'title'					=> 'Пикча: бесплатный фотохостинг',
		'descr'					=> 'Бесплатный сервис хранения изображений',
		'text'					=> '<p><strong>Пикча</strong> - это облачный сервис хранения изображений для всех желающих. Он предназначен <acronym title="Опубликованные фотографии невозможно удалить с сайта!">вечного</acronym> хранения Ваших изображений.</p><p>Для использования <strong>Пикчи</strong> Вам <u><b>не</b></u> нужно регистрироваться, заполнять сотни форм и указывать тонну данных! Просто выберите файл на своём компьютере, и он автоматически загрузится на хостинг! Вы получите ссылку на страницу просмотра файла и прямые ссылки на различные по размеру варианты загруженного Вами изображения. Обратите внимание, что сервис не использует самого понятия удаления изображения, если Вам необходимо стереть тот или иной файл по уважительной причине, пожалуйста, свяжитесь с <a href="mailto:info@'.DOMAIN.'">администрацией</a> сервиса &laquo;Пикча&raquo;.</p>',
		'dev'					=> '<p><strong>Разработчикам</strong>: для удобства работы с хостингом изображений на своём сайте или в своём приложении, Вы можете воспользоваться единственной API-функцией нашего сервиса. Она позволяет загрузить файл на сервер и получить его идентификатор, ссылку на просмотр файла и прямые ссылки на различные размеры загруженного изображения.',
		'copyright'				=> '&copy; 2009-'.date('Y').' <a href="http://'.DOMAIN.'/">Pik4</a>. Все права защищены.',
		'counter'				=> ' ', // LiveInternet or Yandex Metrika 16px height

		// Generic trings
		'input'					=> 'Выберите изображение на своём компьютере, и его загрузка начнётся автоматически',
		'done'					=> 'Ваша картинка успешно загружена на сервер',
		'error'					=> 'Не удалось загрузить изображение на сервер',

		// Display Fields
		'viewer'				=> 'Просмотр картинки',
		'process'				=> 'Сокращаю ссылку, подождите немного ...',
		'full'					=> '<acronym title="Прямая ссылка на изображение размером до 1280х1280">Полный</acronym> размер',
		'image'					=> 'Для <acronym title="Прямая ссылка на уменьшенное изображение размером до 640х640, удобное для просмотра в окне браузера">просмотра</acronym>',
		'thumb'					=> '<acronym title="Прямая ссылка на уменьшенное изображение размером до 128х128">Миниатюра</acronym>',
		'blog'					=> 'Для <acronym title="HTML-код с картинкой среднего размера и ссылкой на эту страницу">блога</acronym>',
		'insert'				=> 'Для <acronym title="HTML-код с картинкой полного размера">вставки</acronym>',

	);

	return $lang[$id] ? $lang[$id] : $id;

} // language get ...
function e( $id ) {
	echo l( $id );
} // micro echo function
//


// Cutting the image
// - $src and $dst - paths to source and destination files
// - $minx and $miny - size of image in pixels
// - $qu - JPEG-image quality in percents
// - $cut - if is true, image will be cropped directly by minx and miny, do not keep aspekt ratio
function image_cut ( $src, $dst, $minx, $miny, $qu, $cut = false ) {

	$img = getimagesize ( $src );
	if ( ! $img ) return false;

	$img_x = $img[0];
	$img_y = $img[1];
	$img_r = $img_x / $img_y;

	switch ( $img[2] ) {
		case 1:	$img_o = imagecreatefromgif  ( $src ); break;
		case 2:	$img_o = imagecreatefromjpeg ( $src ); break;
		case 3:	$img_o = imagecreatefrompng  ( $src ); break;
		default: return false;
	}

	if ( ! $img_o ) return false;

	if ( $cut || ( $img_x > $minx || $img_y > $miny ) ) {

		if ( $cut ) {

			$dst_x = $minx;
			$dst_y = $miny;
			$dst_r = $minx / $miny;

			if ( $dst_r > $img_r) {
				$w = $img_x;
				$h = $img_x / $dst_r;
			} else  {
				$w = $img_y * $dst_r;
				$h = $img_y;
			}

			$x = ( $img_x - $w ) / 2;
			$y = ( $img_y - $h ) / 2;

		} else {

			$dst_r = $minx / $miny;
			if ( $dst_r < $img_r) {
				$dst_x = $minx;
				$dst_y = $minx / $img_r;
			} else  {
				$dst_x = $miny * $img_r;
				$dst_y = $miny;
			}

			$x = 0; $w = $img_x;
			$y = 0; $h = $img_y;

		}

		$dst_o = imagecreatetruecolor ( $dst_x, $dst_y );
		if ( ! $dst_o ) { imagedestroy ($img_o); return false; }

		imagecopyresampled ( $dst_o, $img_o, 0, 0, $x, $y, $dst_x, $dst_y, $w, $h );

		imagejpeg ( $dst_o, $dst, $qu );
		imagedestroy ( $dst_o );

	} else imagejpeg ( $img_o, $dst, $qu );

	imagedestroy ( $img_o );
	return true;

}

//
// Picture Server Functions
//

function pics_process() {

	if (is_uploaded_file( $_FILES['newpic']['tmp_name'] )) {

		// Good Images Only
		if ( $img = getimagesize( $_FILES['newpic']['tmp_name'] ) ) {

			if ( $img[0] > PFS || $img[1] > PFS ) {
				$tmpf = sprintf ( TMP, md5( microtime() . rand(0, 100) ) );
				$r = image_cut( $_FILES['newpic']['tmp_name'], $tmpf, PFS, PFS, 100, false );
				if ( !$r  )  pics_result(array( 'status' => 'error', 'message' => 'toobig' ));
			} else $tmpf = $_FILES['newpic']['tmp_name'];

			// Set up id and paths
			$id		= pics_id ( $tmpf );
			$pf		= pics_path( 'full', $id );
			$pi		= pics_path( 'image', $id );
			$pt		= pics_path( 'thumb', $id );

			// Make directories
			if ( ! is_dir( dirname( $pf ) ) ) {
				mkdir( dirname( $pf ), FOLDER, true );
				mkdir( dirname( $pi ), FOLDER, true );
				mkdir( dirname( $pt ), FOLDER, true );
			}

			// Process file creation
			copy ( $tmpf, $pf );
			unlink( $tmpf );
			image_cut( $pf, $pi, PMS, PMS, 90, false );
			image_cut( $pi, $pt, PTS, PTS, 90, false );

			pics_result(array(
				'status'	=> 'ok',
				'id'		=> $id,
				'view'		=> sprintf( PICV, $id ),
				'full'		=> pics_host ( 'full',	$id ),
				'image'		=> pics_host ( 'image',	$id ),
				'thumb'		=> pics_host ( 'thumb',	$id ),
			));

		} else pics_result(array(  'status' => 'error', 'message' => 'badformat' ));

	} else return false;

}

// Creating ID for the file given
function pics_id ( $src ) {

	$mdf = md5_file( $src );
	$f = array( '0' => '0000', '1' => '0001', '2' => '0010', '3' => '0011', '4' => '0100', '5' => '0101', '6' => '0110', '7' => '0111', '8' => '1000', '9' => '1001', 'a' => '1010', 'b' => '1011', 'c' => '1100', 'd' => '1101', 'e' => '1110', 'f' => '1111' );
	$t = array( '000000' => '0', '000001' => '1', '000010' => '2', '000011' => '3', '000100' => '4', '000101' => '5', '000110' => '6', '000111' => '7', '001000' => '8', '001001' => '9', '001010' => 'a', '001011' => 'b', '001100' => 'c', '001101' => 'd', '001110' => 'e', '001111' => 'f', '010000' => 'g', '010001' => 'h', '010010' => 'i', '010011' => 'j', '010100' => 'k', '010101' => 'l', '010110' => 'm', '010111' => 'n', '011000' => 'o', '011001' => 'p', '011010' => 'q', '011011' => 'r', '011100' => 's', '011101' => 't', '011110' => 'u', '011111' => 'v', '100000' => 'w', '100001' => 'x', '100010' => 'y', '100011' => 'z', '100100' => '-', '100101' => 'A', '100110' => 'B', '100111' => 'C', '101000' => 'D', '101001' => 'E', '101010' => 'F', '101011' => 'G', '101100' => 'H', '101101' => 'I', '101110' => 'J', '101111' => 'K', '110000' => 'L', '110001' => 'M', '110010' => 'N', '110011' => 'O', '110100' => 'P', '110101' => 'Q', '110110' => 'R', '110111' => 'S', '111000' => 'T', '111001' => 'U', '111010' => 'V', '111011' => 'W', '111100' => 'X', '111101' => 'Y', '111110' => 'Z', '111111' => '_' );

	$ps = ''; for ( $i = 0; $i < 32; $i++ ) $ps .= $f[ $mdf{$i} ];
	$ps = explode( '|', trim( chunk_split( $ps, 6, '|' ), '|' ) );
	$mds = ''; foreach ( $ps as $p ) $mds .= $t[ sprintf( "%06d", $p ) ];

	return $mds;

}

// Path to Image File
function pics_path ( $type, $i ) {
	return sprintf( PICS, $type, $i{0}, $i{1}, $i{2}, $i{3}, substr( $i, 4 ) );
}

// Path to Image on Server's Host
function pics_host ( $type, $i ) {
	return sprintf( PICH, $type{0}, $i );
}

// Return Result
function pics_result( $result ) {
	echo json_encode( $result );
	die();
}

//
// Frontent Processing
function frontend() {

	checkdomain();

?><!DOCTYPE html>
<html>
<head>
	<title><?php e('title'); ?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="/style/style.css" />
	<script type="text/javascript" src="/style/jquery.js"></script>
	<script type="text/javascript" src="/style/fileuploader.js"></script>
	<script type="text/javascript">

		$(document).ready(function(){

			new AjaxUpload( 'newpic', {
				action: "<?php echo UPLOAD;?>",
				name: 'newpic',
				onSubmit: function( file, extension ) {
					$("#theform").hide( )
					$("#loader").show( );
					$("#thefieldlist").hide( );
				},
				onComplete: function( file, response ) {
					data = eval( "(" + response + ")" );
					$("#loader").hide( );
					$("#theform").show( )
					$("#thefieldlist").show( );
					if ( data.status == "ok" ) {
						$("#thelink").html	( '<a href="' + data.view + '">' + data.view + '</a>' );
						$("#pviewer").val	( data.view );
						$("#pfull").val		( data.full );
						$("#pimage").val	( data.image );
						$("#pthumb").val	( data.thumb );
						$("#pblog").val		( '<a href="' + data.view + '"><img src="' + data.image + '" alt="<?php echo DOMAIN; ?>" /></a>' );
						$("#pinsert").val	( '<img src="' + data.full + '" alt="<?php echo DOMAIN; ?>" />' );
					} else alert( "<?php e('error'); ?>" );
				}
			} );

			$('#header').click(function() {
				location.href = '<?php echo SERVER; ?>';
			});

		});

	</script>
	<link rel="shortcut icon" type="image/png" href="/favicon.png" />
	<link rel="icon" type="image/png" href="/favicon.png" />
</head>
<body id="pichost">

<div id="container">

	<div id="header">
		<h1><?php e('title'); ?></h1>
		<p><?php e('descr'); ?></p>
	</div>

	<div id="content">

		<div id="about"><?php e('text'); ?></div>

		<div id="theform">
			<div id="thedescr"><?php e('input'); ?></div>
			<div id="thefile"><input type="file" size="77" id="newpic" name="newpic" /></div>
		</div>

		<div id="loader"></div>

		<div id="thefieldlist">
			<div id="theinfo"><?php e('done'); ?></div>
			<div id="thelink"></div>
			<div id="thefields">
				<label for="pviewer"><?php e('viewer'); ?> <input id="pviewer" type="text" value="" /></label>
				<label for="pfull"><?php e('full'); ?> <input id="pfull" type="text" value="" /></label>
				<label for="pimage"><?php e('image'); ?> <input id="pimage" type="text" value="" /></label>
				<label for="pthumb"><?php e('thumb'); ?> <input id="pthumb" type="text" value="" /></label>
				<label for="pblog"><?php e('blog'); ?> <input id="pblog" type="text" value="" /></label>
				<label for="pinsert"><?php e('insert'); ?> <input id="pinsert" type="text" value="" /></label>
			</div>
		</div>

		<div id="dev"><?php e('dev'); ?></div>

	</div>

	<div id="social">
		<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
		<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="icon" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,lj,friendfeed,moikrug"></div>
	</div>

	<div id="footer">
		<div id="counter"><?php e('counter'); ?></div>
		<div id="copyright"><?php e('copyright'); ?></div>
	</div>

</div>

</body>
</html><?php

}
//

//
// Frontent Processing
function show( $id = null ) {

	checkdomain();
	if ( !$id ) return frontend();

?><!DOCTYPE html>
<html>
<head>
	<title><?php e('title'); ?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="/style/style.css" />
	<script type="text/javascript" src="/style/jquery.js"></script>
	<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?52"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#header').click(function() {
				location.href = '<?php echo SERVER; ?>';
			});
		});
		VK.init({apiId: <?=VKAPP;?>, onlyWidgets: true});
	</script>
	<link rel="shortcut icon" type="image/png" href="/favicon.png" />
	<link rel="icon" type="image/png" href="/favicon.png" />
</head>
<body id="pichost">

<div id="container">

	<div id="header">
		<h1><?php e('title'); ?></h1>
		<p><?php e('descr'); ?></p>
	</div>

	<div id="content">

		<div id="theimage"><a href="<?php printf( PICF, $id ); ?>"><img src="<?php printf( PICI, $id ); ?>" alt="" /></a></div>

		<div id="thelike">
			<div id="vk_like"></div>
			<script type="text/javascript">VK.Widgets.Like( "vk_like", {type: "button", height: 24, pageImage: "<?php printf( PICI, $id ); ?>", pageUrl: "<?php printf( PICV, $id ); ?>"}, "<?php echo $id; ?>" );</script>
		</div>

		<div id="thefields">
			<label for="pviewer"><?php e('viewer'); ?> <input id="pviewer" type="text" value="<?php printf( PICV, $id ); ?>" /></label>
			<label for="pfull"><?php e('full'); ?> <input id="pfull" type="text" value="<?php printf( PICF, $id ); ?>" /></label>
			<label for="pimage"><?php e('image'); ?> <input id="pimage" type="text" value="<?php printf( PICI, $id ); ?>" /></label>
			<label for="pthumb"><?php e('thumb'); ?> <input id="pthumb" type="text" value="<?php printf( PICT, $id ); ?>" /></label>
			<label for="pblog"><?php e('blog'); ?> <input id="pblog" type="text" value="<a href=&quot;<?php printf( PICV, $id ); ?>&quot;><img src=&quot;<?php printf( PICI, $id ); ?>&quot; alt=&quot;<?php echo DOMAIN; ?>&quot; /></a>" /></label>
			<label for="pinsert"><?php e('insert'); ?> <input id="pinsert" type="text" value="<img src=&quot;<?php printf( PICF, $id ); ?>&quot; alt=&quot;<?php echo DOMAIN; ?>&quot; />" /></label>
		</div>

	</div>

	<div id="social">
		<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
		<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="icon" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,lj,friendfeed,moikrug"></div>
	</div>

	<div id="discuss">
		<div id="discuss-block"><div id="vk_comments"></div></div>
		<script type="text/javascript">VK.Widgets.Comments( "vk_comments", {limit: 10, width: "570", attach: "*", autoPublish: 0, pageUrl: "<?php printf( PICV, $id ); ?>" }, "<?php echo $id; ?>" );</script>
	</div>

	<div id="footer">
		<div id="counter"><?php e('counter'); ?></div>
		<div id="copyright"><?php e('copyright'); ?></div>
	</div>

</div>

</body>
</html><?php

}
//

//
// Check the address
function checkdomain() {
	if ( $_SERVER['HTTP_HOST'] != DOMAIN ) {
		header( 'Location: http://' . DOMAIN . $_SERVER['REQUEST_URI'] );
		die();
	}
}
//

//
// Program Flow
$id = isset( $_GET['id'] ) ? preg_replace( '#([^0-9a-zA-Z\-\_]+)#i', '', $_GET['id'] ) : null;
if ( is_uploaded_file( $_FILES['newpic']['tmp_name'] ) ) {
	pics_process();
	header( 'Location: ' . sprintf( PICV, '' ) );
}elseif ( $id ) {
	show( $id );
} else frontend();
//

// end. =)
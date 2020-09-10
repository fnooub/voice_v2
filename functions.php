<?php

function base_url($uri = '')
{
	return 'https://eapal.herokuapp.com/' . $uri;
}

function get_var($param)
{
	return isset($_GET['' . $param . '']) ? $_GET['' . $param . ''] : null;
}

function post_var($param)
{
	return isset($_POST['' . $param . '']) ? $_POST['' . $param . ''] : null;
}

function get_row($s, $e, $input, $quote = FALSE)
{
	if ($quote === TRUE) {
		$s = preg_quote($s);
		$e = preg_quote($e);
	}
	preg_match('@' . $s . '(.+?)' . $e . '@si', $input, $output);
	return $output[1];
}

function get_rows($s, $e, $input, $quote = FALSE)
{
	if ($quote === TRUE) {
		$s = preg_quote($s);
		$e = preg_quote($e);
	}
	preg_match_all('@' . $s . '(.+?)' . $e . '@si', $input, $output);
	return $output[1];
}

function get_links($input)
{
	preg_match_all('/href=([\"\'])\s*(.+?)\s*\1/', $input, $output);
	return $output[2];
}

function wp_strip_all_tags( $string, $remove_breaks = false ) {
	$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
	$string = strip_tags( $string, '<p><br>' );

	if ( $remove_breaks ) {
		$string = preg_replace( '/[\r\n\t ]+/', ' ', $string );
	}

	return trim( $string );
}

function single_curl($link)
{
	// Tạo mới một cURL
	$ch = curl_init();

	// Cấu hình cho cURL
	curl_setopt($ch, CURLOPT_URL, $link); // Chỉ định địa chỉ lấy dữ liệu
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36'); // Giả tên trình duyệt $_SERVER['HTTP_USER_AGENT']
	curl_setopt($ch, CURLOPT_HEADER, 0); // Không kèm header của HTTP Reponse trong nội
	curl_setopt($ch, CURLOPT_TIMEOUT, 600); // Định timeout khi curl
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Trả kết quả về ở hàm curl_exec
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // Không xác nhận chứng chì ssl

	// Thực thi cURL
	$result = curl_exec($ch);

	// Ngắt cURL, giải phóng
	curl_close($ch);

	return $result;

}

function multi_curl($links){
	$mh = curl_multi_init();
	foreach($links as $k => $link) {
		$ch[$k] = curl_init();
		curl_setopt($ch[$k], CURLOPT_URL, $link);
		curl_setopt($ch[$k], CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36');
		curl_setopt($ch[$k], CURLOPT_HEADER, 0);
		curl_setopt($ch[$k], CURLOPT_TIMEOUT, 0);
		curl_setopt($ch[$k], CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch[$k], CURLOPT_SSL_VERIFYPEER, 0);
		curl_multi_add_handle($mh, $ch[$k]);
	}
	$running = null;
	do {
		curl_multi_exec($mh, $running);
	} while($running > 0);
	foreach($links as $k => $link) {
		$result[$k] = curl_multi_getcontent($ch[$k]);
		curl_multi_remove_handle($mh, $ch[$k]);
	}
	curl_multi_close($mh);
	return join('', $result);

}

// lọc thẻ p vào nội dung văn bản
function nl2p($string, $nl2br = true)
{
	// Normalise new lines
	$string = str_replace(array("\r\n", "\r"), "\n", $string);

	// Extract paragraphs
	$parts = explode("\n", $string);

	// Put them back together again
	$string = '';

	foreach ($parts as $part) {
		$part = trim($part);
		if ($part) {
			if ($nl2br) {
				// Convert single new lines to <br />
				$part = nl2br($part);
			}
			$string .= "<p>$part</p>\n";
		}
	}

	return $string;
}

function slug($link)
{
	$a_str = array('ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'á', 'à', 'ả', 'ã', 'ạ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ');
	$d_str = array('đ', 'Đ');
	$e_str = array('é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ', 'É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ');
	$o_str = array('ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ');
	$i_str = array('í', 'ì', 'ỉ', 'ị', 'ĩ', 'Í', 'Ì', 'Ỉ', 'Ị', 'Ĩ');
	$u_str = array('ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ữ', 'ử', 'ự', 'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự');
	$y_str = array('ý', 'ỳ', 'ỷ', 'ỵ', 'ỹ', 'Ý', 'Ỳ', 'Ỷ', 'Ỵ', 'Ỹ');

	$link = str_replace($a_str, 'a', $link);
	$link = str_replace($d_str, 'd', $link);
	$link = str_replace($e_str, 'e', $link);
	$link = str_replace($o_str, 'o', $link);
	$link = str_replace($i_str, 'i', $link);
	$link = str_replace($u_str, 'u', $link);
	$link = str_replace($y_str, 'y', $link);

	$link = strtolower($link); //chuyển tất cả sang chữ thường
	$link = preg_replace('/[^a-z0-9]/', ' ', $link); //ngoài a-z0-9 thì chuyển sang khoảng trắng
	$link = preg_replace('/\s\s+/', ' ', $link); //2 khoảng trắng trở lên thì chỉ lấy 1
	$link = trim($link); //loại bỏ khoảng trắng đầu cuối
	$link = str_replace(' ', '-', $link); //chuyển khoảng trắng sang gạch ngang (-)
	return $link;
}

/**
 * url slug tieng viet
 */
if ( ! function_exists('url_slug'))
{
	/**
	 * Create URL Title
	 *
	 * Takes a "title" string as input and creates a
	 * human-friendly URL string with a "separator" string
	 * as the word separator.
	 *
	 * @param	string	$str		Input string
	 * @param	string	$separator	Word separator
	 *			(usually '-' or '_')
	 * @param	bool	$lowercase	Whether to transform the output string to lowercase
	 * @return	string
	 */
	function url_slug($str, $separator = '-', $lowercase = FALSE)
	{
		$characters = array(
			'/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/' => 'a',
			'/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/' => 'e',
			'/ì|í|ị|ỉ|ĩ/' => 'i',
			'/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/' => 'o',
			'/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/' => 'u',
			'/ỳ|ý|ỵ|ỷ|ỹ/' => 'y',
			'/đ/' => 'd',
			'/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/' => 'A',
			'/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/' => 'E',
			'/Ì|Í|Ị|Ỉ|Ĩ/' => 'I',
			'/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/' => 'O',
			'/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/' => 'U',
			'/Ỳ|Ý|Ỵ|Ỷ|Ỹ/' => 'Y',
			'/Đ/' => 'D'
		);

		$str = preg_replace(array_keys($characters), array_values($characters), $str);
		if ($separator === 'dash')
		{
			$separator = '-';
		}
		elseif ($separator === 'underscore')
		{
			$separator = '_';
		}		
		if ($lowercase === TRUE)
		{
			$str = strtolower($str);
		}
		$str = preg_replace('/[^A-Za-z0-9]/', ' ', $str); //ngoài a-z0-9 thì chuyển sang khoảng trắng
		$str = preg_replace('/\s\s+/', ' ', $str); //2 khoảng trắng trở lên thì chỉ lấy 1
		$str = trim($str); //loại bỏ khoảng trắng đầu cuối
		$str = str_replace(' ', $separator, $str);
		return $str;
	}
}

function loc($word)
{
	$word = html_entity_decode($word);
	// loc chu
	$word = preg_replace(array('/\bria\b/iu', '/\bsum\b/iu', '/\bboa\b/iu', '/\bmu\b/iu', '/\bah\b/iu', '/\buh\b/iu', '/\bcm\b/iu', '/\bkm\b/iu', '/\bkg\b/iu', '/\bcmn\b/iu', '/\bgay go\b/iu'), array('dia', 'xum', 'bo', 'mư', 'a', 'ư', 'xen ti mét', 'ki lô mét', 'ki lô gam', 'con mẹ nó', 'khó khăn'), $word);
	// loc ki tu dac biet
	$word = preg_replace('/…/', '...', $word);
	$word = preg_replace('/\.(?:\s*\.)+/', '...', $word);
	$word = preg_replace('/,(?:\s*,)+/', ',', $word);
	$word = preg_replace('/-(?:\s*-)+/', '', $word);
	$word = preg_replace('/-*o\s*(0|O)\s*o-*/', '...', $word);
	$word = preg_replace('/~/', '-', $word);
	$word = preg_replace('/\*/', '', $word);
	$word = preg_replace('/ +(\.|\?|!|,)/', '$1', $word);
	// thay the
	$word = str_replace('"..."', '"Lặng!"', $word);
	return $word;
}

function loc_title($text)
{
	$text = preg_replace('/[^a-z0-9A-Z[:space:]àáãạảăắằẳẵặâấầẩẫậèéẹẻẽêềếểễệđìíĩỉịòóõọỏôốồổỗộơớờởỡợùúũụủưứừửữựỳỵỷỹýÀÁÃẠẢĂẮẰẲẴẶÂẤẦẨẪẬÈÉẸẺẼÊỀẾỂỄỆĐÌÍĨỈỊÒÓÕỌỎÔỐỒỔỖỘƠỚỜỞỠỢÙÚŨỤỦƯỨỪỬỮỰỲỴỶỸÝ]/u', '', $text);
	$text = preg_replace('/0+(\d)/', '$1', $text);
	return $text;
}

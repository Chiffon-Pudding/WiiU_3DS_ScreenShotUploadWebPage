<?php
declare(strict_types=1);
header("Content-type: text/html; charset=utf-8");

// Settings

$size_limit = 2000000;
$save_file_name_random_bytecode_digits = 3; // ファイル名真ん中に付く衝突防止用ランダムバイトコードの桁数。指定数xバイト分。
$auto_mkdir = true;
$auto_chmod_dir = true;
$upload_file_permission = 0644;
$allow_file_jpeg = true;
$allow_nonstandard_res_jpeg = true;
$allow_file_mkv = true;
$allow_file_unknown = false;

// Save dir ... $save_dir_root/$save_dir_hardware/$save_dir_hardware_type.

$save_dir_root = './picture/';

$save_dir_wiiu = 'wiiu/';
$save_dir_3ds = '3ds/';
$save_dir_unknown = 'unknown/';

$save_dir_wiiu_tv = 'tv/';
$save_dir_wiiu_gamepad = 'gamepad/';
$save_dir_3ds_photo = 'photo/';
$save_dir_3ds_video = 'video/';
$save_dir_3ds_dual = 'dual/';
$save_dir_3ds_upper = 'upper/';
$save_dir_3ds_lower = 'lower/';
$save_dir_3ds_gamememo = 'gamememo/';


// Strings

// webpage title
$mes_webpage_title = 'WiiU/3DS スクリーンショットアップローダ(べーた)';

// top messages
$mes_select_file = 'ファイルを選択してください。';

// button
$mes_send_button = '送信する';

// result messages
$mes_upload_successed = 'ファイルのアップロードに成功しました。';
$mes_upload_failed = 'ファイルのアップロードに失敗しました。';
$mes_upload_successed_color = 'green';
$mes_upload_failed_color = 'red';

// result info messeages
$mes_result = '送信結果';
$mes_saved_file_name = '保存されたファイル名 : ';
$mes_file_size = 'ファイルサイズ :';
$mes_mime_contents_type = 'ファイルのMIMEタイプ : ';
$mes_image_resolution = '画像の解像度 : ';
$mes_file_type_analysis_results = 'ファイルの種別 : ';

// file type analysis results
$mes_analyzed_wiiu_tv = 'WiiU/テレビ画面';
$mes_analyzed_wiiu_gamepad = 'WiiU/ゲームパッド';
$mes_analyzed_3ds_photo = '3DS/写真';
$mes_analyzed_3ds_video = '3DS/動画';
$mes_analyzed_3ds_dual = '3DS/上下両画面';
$mes_analyzed_3ds_upper = '3DS/上画面';
$mes_analyzed_3ds_lower = '3DS/下画面';
$mes_analyzed_3ds_gamememo = '3DS/ゲームメモ';
$mes_analyzed_unknown = '不明';

// error messages
$mes_error_no_file_selected = 'ファイルが選択されていません。';
$mes_error_file_too_large = 'ファイルサイズが大きすぎます。';
$mes_error_unknown = '不明なエラーが発生しました。';
$mes_jpeg_file_not_allowed = 'JPEGファイルはこのサーバではサポートされていません。';
$mes_matroska_video_file_not_allowed = 'Matroska Video Fileはこのサーバではサポートされていません。';
$mes_jpeg_file_not_allowed_resolution = 'アップロードされたJPEGファイルの解像度はこのサーバではサポートされていません。';
$mes_unknown_file_not_allowed = 'アップロードされたファイルのMIMEタイプはこのサーバではサポートされていません。';



if(isset($_FILES['uploaded_file']['error']) && is_int($_FILES['uploaded_file']['error'])){

    try {

        switch ($_FILES['uploaded_file']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException($mes_error_no_file_selected);
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException($mes_error_file_too_large);
                break;
            default:
                throw new RuntimeException($mes_error_unknown);
                break;
        }

        $save_file_size = $_FILES['uploaded_file'][size];
        if ($save_file_size > $size_limit) {
            throw new RuntimeException($mes_error_file_too_large);
        }

        $save_file_mimetype = mime_content_type($_FILES['uploaded_file']['tmp_name']);
        
        switch ($save_file_mimetype){
            case 'image/jpeg':
                if($allow_file_jpeg){
                    $save_file_res = getimagesize($_FILES['uploaded_file']['tmp_name'])[3];
                    switch ($save_file_res){
                        case 'width="1280" height="720"':
                            $save_file_dir = $save_dir_root . $save_dir_wiiu . $save_dir_wiiu_tv;
                            $file_type_analysis_results  = $mes_analyzed_wiiu_tv;
                            break;
                        case 'width="854" height="480"':
                            $save_file_dir = $save_dir_root . $save_dir_wiiu . $save_dir_wiiu_gamepad;
                            $file_type_analysis_results  = $mes_analyzed_wiiu_gamepad;
                            break;
                        case 'width="640" height="480"':
                            $save_file_dir = $save_dir_root . $save_dir_3ds . $save_dir_3ds_photo;
                            $file_type_analysis_results  = $save_dir_3ds_photo;
                            break;
                        case 'width="432" height="528"':
                            $save_file_dir = $save_dir_root . $save_dir_3ds . $save_dir_3ds_dual;
                            $file_type_analysis_results  = $mes_analyzed_3ds_dual;
                            break;
                        case 'width="400" height="240"':
                            $save_file_dir = $save_dir_root . $save_dir_3ds . $save_dir_3ds_upper;
                            $file_type_analysis_results  = $mes_analyzed_3ds_upper;
                            break;
                        case 'width="320" height="240"':
                            $save_file_dir = $save_dir_root . $save_dir_3ds . $save_dir_3ds_lower;
                            $file_type_analysis_results  = $mes_analyzed_3ds_lower;
                            break;
                        case 'width="320" height="216"':
                            $save_file_dir = $save_dir_root . $save_dir_3ds . $save_dir_3ds_gamememo;
                            $file_type_analysis_results  = $mes_analyzed_3ds_gamememo;
                            break;
                        default:
                            if($allow_nonstandard_res_jpeg){
                                $save_file_dir = $save_dir_root . $save_dir_unknown;
                                $file_type_analysis_results  = $mes_analyzed_unknown;
                            } else {
                                throw new RuntimeException($mes_jpeg_file_not_allowed_resolution);
                            }
                            break;
                    }
                } else {
                    throw new RuntimeException($mes_jpeg_file_not_allowed);
                }
                break;
            case 'video/x-matroska':
                if($allow_file_mkv){
                    $save_file_dir = $save_dir_root . $save_dir_3ds . $save_dir_3ds_video;
                    $file_type_analysis_results  = $mes_analyzed_3ds_video;
                } else {
                    throw new RuntimeException($mes_matroska_video_file_not_allowed);
                }
                break;
            default:
                if($allow_file_unknown){
                    $save_file_dir = $save_dir_root . $save_dir_unknown;
                    $file_type_analysis_results  = $mes_analyzed_unknown;
                } else {
                    throw new RuntimeException($mes_unknown_file_not_allowed);
                }
                break;
        }

        $save_file_name = date('YmdHis') . '_' . bin2hex(openssl_random_pseudo_bytes($save_file_name_random_bytecode_digits)) . "_" . basename($_FILES['uploaded_file']['name']);

        $upload_file_path = $save_file_dir . $save_file_name;

        if($auto_mkdir && !is_dir($save_file_dir)){
            mkdir($save_file_dir, 0777, true);
        } elseif($auto_chmod_dir) {
            chmod($save_file_dir,0777);
        }

        if(!file_exists($upload_file_path) && move_uploaded_file($_FILES['uploaded_file']['tmp_name'],$upload_file_path)){
            chmod($upload_file_path, $upload_file_permission);
            $msg = [$mes_upload_successed_color , $mes_upload_successed];
        } else {
            throw new RuntimeException($mes_upload_failed);
        }
    } catch (RuntimeException $e) {
        $msg = [$mes_upload_failed_color, $e->getMessage()];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title><?=$mes_webpage_title?></title>
</head>
<body>
<form action="" method="POST" enctype="multipart/form-data">
    <fieldset>
        <legend><?=$mes_select_file?></legend>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?=$size_limit?>" />
        <input type="file" name="uploaded_file" />
        <input type="submit" value="<?=$mes_send_button?>" />
    </fieldset>
</form>
<?php if (isset($msg)): ?>
    <fieldset>
        <legend><?=$mes_result?></legend>
        <span style="color:<?=$msg[0]?>;"><?=$msg[1]?></span>
        <p>
            <?=$mes_saved_file_name?><?=$save_file_name?><br />
            <?=$mes_file_size?><?=$save_file_size?><br />
            <?=$mes_mime_contents_type?><?=$save_file_mimetype?><br />
            <?=$mes_image_resolution?><?=$save_file_res?><br />
            <?=$mes_file_type_analysis_results?><?=$file_type_analysis_results ?>
        </p>
    </fieldset>
<?php endif; ?>
</body>
</html>
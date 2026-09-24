@props([
    'image' => null,
    'alt' => '',
    'class' => '',
    'preset' => 'gallery',
    'eager' => false,
    'subfolder' => 'postsimg',
])

{!! \App\Helpers\ImageHelper::renderPicture($image, $alt, $class, $preset, $eager, $subfolder) !!}

<?php
$tmp = __DIR__ . '/test.jpg';
file_put_contents($tmp, 'fake image data');
var_dump(mime_content_type($tmp));
unlink($tmp);

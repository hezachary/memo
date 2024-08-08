<?php
        $s = "id	test_1_id	test_2_id	content_1 content_2
1	11	111	text_1	123.00
2	22	222	text_2	456.00
3	33	333	text_3	789.00";
        $ignore = explode("\t","id	test_1_id	test_2_id");

        $arr = explode("\n", $s);

        $key = explode("\t", $arr[0]);
        $result = [];
        for ($i = 1; $i < count($arr); $i++) {
            $result[] = array_diff_key(array_combine($key, explode("\t", $arr[$i])), array_flip($ignore));
        }
        file_put_contents(
            'whatever.json',
            json_encode(
                $result,
                JSON_PRETTY_PRINT
            )
        );
        echo 'Done';

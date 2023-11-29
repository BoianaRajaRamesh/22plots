<?php

function convert_video_to_gif($directory_path_full, $gif_path) {
//     ffmpeg -ss 0 -t 5 -i D:\video.mp4 -vf scale=-1:-1 -r 2 D:\my_gif.gif
    exec("ffmpeg -ss 0 -t 5 -i $directory_path_full -vf scale=360:-1 -r 5 $gif_path");
    return TRUE;
}

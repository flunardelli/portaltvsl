FFMPEG Video.module helper
==========================

See : http://video.heidisoft.com/docs/users-guide-26

This helper module facilitates uploading new videos using the video module. It
features a batch processing queue for videos to be transcoded and automatic
thumbnail generation.

IMPORTANT: the ffmpeg helper currently only works on unix based environment. It currently doesn't support Windows based servers.

Install instructions
--------------------

1. Activate the video_ffmpeg_helper module
2. Setup it's advanced options to meet your needs
3. Move (or symlink) video_render.php and video_scheduler.php into your Drupal root
4. Edit the first "Configuration" code lines of those files to meet your needs (IMPORTANT: path to ffmpeg executable on your server)
5. Check permissions of the files and folders (/tmp/video and files/* must be writable by the webserver or the user executling the cron job)
6. You now have two options to execute the video_scheduler.php script:

  6.1 (default) Enable the execution of video_scheduler.php using standard drupal cron.
      GMM: 07/23/2009 Note: ** Do not use this method if your Apache server is running with suPHP **
           As of this writing, suPHP will cause the Drupal cron to loop infinitely and no video 
           will be rendered. In this case crontab should be used instead of Drupal cron!

  6.2 Schedule the execution of video_scheduler.php using unix cron

    The crontab should look something like this:
    
    # m     h       dom     mon     dow     user            command
    */20     *       *       *       *       www-data        cd /absolute/path/to/drupal/ ; php video_scheduler.php http://www.example.com/path_to_drupal
    
    This will execute the video_scheduler every 20 minutes.
    
    Note that the video_scheduler doesn't produce any output and cannot be called
    from the web. It will, however, put some information in the watchdog.
    


Troubleshooting
------------------------

Configuring and installing ffmpeg in a web server environment might be pretty difficult. In order to help you troubleshoot the transcoding process the ffmpeg helper puts debugging informations on the drupal logs. I strongly suggest to have a look at them if you are experiencing problems with transcoding.

The ffmpeg puts in the drupal logs the commands it was trying to execute. You might try to rerun them on a command shell in order understand what went wrong.
  

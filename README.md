# BootCMS 2.0 Development
BootCMS 2.0 Development

# Server
"Directory APP_PATH/cache must be writable."
chmod o+w /application/cache

# Nginx
     if (!-e $request_filename){
          rewrite ^/(.*)$ /index.php/$1 last;
     }

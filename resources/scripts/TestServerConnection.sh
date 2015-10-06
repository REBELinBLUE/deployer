### Test server connection - {{ server_id }}
# Ensure the directory exists and can be written to
cd {{ project_path }}
ls

# Test for a php-fpm process
if [ ! -z "$(ps -ef | grep -v grep | grep php-fpm)" ]; then
    sudo /usr/sbin/service php5-fpm restart
fi

# Ensure it can be written to
touch {{ test_file }}
echo "testing" >> {{ test_file }}
chmod +x {{ test_file }}
rm {{ test_file }}

# Ensure directories can be made
mkdir {{ test_directory }}

touch {{ test_directory }}/{{ test_file }}
echo "testing" >> {{ test_directory }}/{{ test_file }}
chmod +x {{ test_directory }}/{{ test_file }}

ls {{ test_directory }}/

rm -rf {{ test_directory }}

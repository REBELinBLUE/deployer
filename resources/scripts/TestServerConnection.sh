set -e
cd $server->path
ls
touch $tmpfile
echo "testing" >> $tmpfile
chmod +x $tmpfile
rm $tmpfile
mkdir $tmpdir
touch $tmpdir/$tmpfile
echo "testing" >> $tmpdir/$tmpfile
chmod +x $tmpdir/$tmpfile
ls $tmpdir/
rm -rf $tmpdir

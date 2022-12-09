echo "1/3 Building frontend..."
cd client
npm run build
rm build/index.html
echo "2/3 Creating api folder..."
cd ../server
echo -n > config.php
mkdir files2
cp files/.htaccess files2
rm -rf files/
cp files2 files
cd ..
mkdir api
cp server/index.php api
cp server/.htaccess api
cp server/composer.json api
cp server/composer.lock api
cp server/controllers api
cp server/data api
cp server/libs api
cp server/models api
cp server/vendor api
cp server/files api
cp server/config.php api
chmod -R 755 .
cp client/src/index.php client/build
echo "3/3 Generating zip..."
cd client/build
zip opensupports_dev.zip index.php
zip -u opensupports_dev.zip .htaccess
zip -u opensupports_dev.zip bundle.js
zip -ur opensupports_dev.zip images
cp opensupports_dev.zip ../..
cd ../..
zip -ur opensupports_dev.zip api
mkdir dist
cp opensupports_dev.zip dist

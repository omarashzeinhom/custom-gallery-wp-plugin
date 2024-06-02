# Custom WordPress Gallery Plugin For PlayList

- Project Structure

```bash
/plugin-name
     plugin-name.php
     uninstall.php
     /languages
     /includes
     /admin
          /js
          /css
          /images
     /public
          /js
          /css
          /images
```

## Start By

1. Install  Dev Dependencies using `yarn`.

```bash
npm install -g yarn
yarn install
```

2. Start Development Server 

```bash
gulp
#or 
npx gulp
```

3. Start NodeLiveReload

```bash
node livereload.js
```

### References

1. [Best Practices - WordPress Plugin HandBook](https://developer.wordpress.org/plugins/plugin-basics/best-practices/)
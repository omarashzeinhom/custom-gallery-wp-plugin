# Custom WordPress Gallery Plugin For PlayList

- Project Structure

```bash
/custom-gallery-plugin
     custom-gallery-plugin.php
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

2. [DevinVinson - WordPress-Plugin-Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/tree/master)

3. [ptahdunbar - A WordPress Skeleton Plugin](https://github.com/ptahdunbar/wp-skeleton-plugin)

4. [wp scaffold plugin](https://developer.wordpress.org/cli/commands/scaffold/plugin/)

5. [JJJ SLASH Architecture – My approach to building WordPress plugins](https://jjj.blog/2012/12/slash-architecture-my-approach-to-building-wordpress-plugins/)

6. [Implementing the MVC Pattern in WordPress Plugins - Ian Dunn](https://iandunn.name/content/presentations/wp-oop-mvc/mvc.php#/)

7. [Activation / Deactivation Hooks - WordPress Plugin HandBook](https://developer.wordpress.org/plugins/plugin-basics/activation-deactivation-hooks/)
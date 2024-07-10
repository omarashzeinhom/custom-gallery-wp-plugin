# MiniGallery

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

- HeaderFields

1. [Best Practices - WordPress Plugin HandBook](https://developer.wordpress.org/plugins/plugin-basics/best-practices/)

- Project Structure

1. [DevinVinson - WordPress-Plugin-Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/tree/master)

2. [ptahdunbar - A WordPress Skeleton Plugin](https://github.com/ptahdunbar/wp-skeleton-plugin)

3. [wp scaffold plugin](https://developer.wordpress.org/cli/commands/scaffold/plugin/)

4. [JJJ SLASH Architecture – My approach to building WordPress plugins](https://jjj.blog/2012/12/slash-architecture-my-approach-to-building-wordpress-plugins/)

5. [Implementing the MVC Pattern in WordPress Plugins - Ian Dunn](https://iandunn.name/content/presentations/wp-oop-mvc/mvc.php#/)

- Activation / Deactivation Hooks

1. [Activation / Deactivation Hooks - WordPress Plugin HandBook](https://developer.wordpress.org/plugins/plugin-basics/activation-deactivation-hooks/)

- General References

[HTML <input> multiple Attribute](https://www.w3schools.com/tags/att_input_multiple.asp)
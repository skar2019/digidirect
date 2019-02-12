# Lego Base theme

Abstract parent base theme is a set of flexible, independent and scalable solutions, which are used in the development of modules and the child themes.


The theme provides the ability to customize all of the following user interface elements:

* block-grid


### Lego Base theme structure

```css
/web/
    ├── css/
    │    ├── style-guide/
    │    │    ├── source/
    │    │    │    ├── _block-grid.less
    │    │    │    └── README.md
    │    │    │
    │    │    └─── index.html
    │    │
    │    ├── source/
    │    │    ├── components/ewave (Reusable components files)
    │    │    │    └── _components.less
    │    │    │
    │    │    ├── components/vendor (3rd-party components files)
    │    │    │    └── _vendors.less
    │    │    │
    │    │    └── lib/ewave (Library source files)
    │    │         ├── variables/ (Decoupled variables)
    │    │         │     └── _variables.less
    │    │         │
    │    │         └── _lib.less
    │    │
    │    └── _styles.less
    │
    │
    └── js/ (Library javascript files)
         ├─ vendor/
         │    └── ...
         │
         └─ components/
              └── ...

```
&nbsp;

### Installation

[Wiki link](https://wiki.ewave.com/pages/viewpage.action?pageId=21566293)
Carbon Breadcrumbs Woocommerce
==============================

An extensible WooCommerce extension for the [Carbon Breadcrumbs](https://github.com/tyxla/carbon-breadcrumbs) plugin.

Adds additional WooCommerce-related breadcrumb items, and provides additional settings and options, as well as extensibility capabilities for developers.

Actions & Filters
------

The following actions and filters can allow developers to modify the default behavior and hook to add custom functionality in various situations. For additional actions, refer to the readme of the [main Carbon Breadcrumbs plugin](https://github.com/tyxla/carbon-breadcrumbs).

- - -

### Filters

#### wc\_get\_template

**$located** *(string)*. The original template path.

**$template_name** *(string)*. The original template name.

**$args** *(array)*. The args that the breadcrumbs function was called with.

**$template_path** *(string)*. Path to templates.

**$default_path** *(string)*. The default path to templates.

This filter is default for WooCommerce and is applied before loading a certain template. It allows you to modify the location of the template that will be used for the WooCommerce breadcrumbs.

- - -

### Actions

#### carbon\_breadcrumbs\_woocommerce\_before\_setup\_trail

**$trail** *(Carbon\_Breadcrumb\_Trail)*. The breadcrumb trail object.

This action allows you to modify the breadcrumb trail object before the setup (which adds the WooCommerce breadcrumb items) has started.

#### carbon\_breadcrumbs\_woocommerce\_after\_setup\_trail

**$trail** *(Carbon\_Breadcrumb\_Trail)*. The breadcrumb trail object.

This action allows you to modify the breadcrumb trail object after the setup (which adds the WooCommerce breadcrumb items) has been completed.
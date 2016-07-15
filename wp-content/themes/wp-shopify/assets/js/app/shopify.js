import ShopifyBuy from 'shopify-buy';
import Rx from 'rx';
import $ from 'jquery';
import RequestPromise from 'request-promise';


//
// Grabbing Shopify credentials from WordPress
//
const getShopifyCreds = () => {

  var options = {
    method: 'GET',
    uri: '/wp/wp-admin/admin-ajax.php',
    json: true,
    data: {
      action: 'app_get_credentials'
    }
  };

  return RequestPromise(options);

};


//
// Check for existing credentials in local storage
//
const hasExistingCredentials = function() {
  return localStorage.getItem('shopifyAPICredentials');
};


//
// Subscribing to AJAX events
//
const onResponse = function(promise, successCallback, errorCallback) {

  return Rx.Observable.fromPromise(promise).subscribe(
    (promiseData) => {
      console.log('Creds Next: %s', promiseData);
      return successCallback(promiseData);
    },
    (err) => {
      console.log('Creds Error: %s', err);
      errorCallback();
    }
  );

};


//
// Initialize Shopify, return client object
//
const shopifyInit = (creds) => {

  let shopify = ShopifyBuy.buildClient({
    apiKey: creds.shopifyAPIKey,
    myShopifyDomain: creds.shopifyDomain,
    appId: creds.shopifyAppId
  });

  return shopify;

};


//
// Response Error
//
const responseError = function() {
  console.log('Death be to thee who does not make program work.');
};


//
// Format number into dollar amount
//
const formatAsMoney = amount => {
  return '$' + parseFloat(amount, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
};


//
// Get Single Product by ID
//
const getProduct = function(shopify, productId) {

  // Returns a promise
  let productPromise = shopify.fetchProduct(productId);

  return productPromise;

};


//
// Add 'quantity' amount of product 'variant' to cart
//
const addVariantToCart = function(variant, quantity) {
  openCart();

  cart.addVariants({ variant: variant, quantity: quantity }).then(function() {
    renderCartItems();
  }).catch(function (errors) {
    console.log('Fail');
    console.error(errors);
  });

  updateCartTabButton();
};


//
// Open Cart
//
const openCart = () => {
  $('.cart').addClass('js-active');
};


//
// Close Cart
//
const closeCart = () => {
  $('.cart .btn--close').click(function () {
    $('.cart').removeClass('js-active');
  });
};


//
// Update cart tab button
//
const updateCartTabButton = function() {
  if (cart.lineItems.length > 0) {
    var totalItems = cart.lineItems.reduce(function(total, item) {
      return total + item.quantity;
    }, 0);
    $('.btn--cart-tab .btn__counter').html(totalItems);
    $('.btn--cart-tab').addClass('js-active');
  } else {
    $('.btn--cart-tab').removeClass('js-active');
    $('.cart').removeClass('js-active');
  }

  $('.btn--cart-tab').click(function() {
    openCart();
  });
};


//
// Checkout listener
//
const attachCheckoutButtonListeners = function () {
  $('.btn--cart-checkout').on('click', function () {
    window.open(cart.checkoutUrl, '_self');
  });
};


//
// Updating product
//
const updateProduct = product => {

  var selectedVariant = product.selectedVariant;
  var selectedVariantImage = product.selectedVariantImage;
  var currentOptions = product.options;

  var productOptions = createProductOptions(product);
  $('.variant-selectors').html(productOptions);

  updateProductTitle(product.title);
  updateVariantImage(selectedVariantImage);
  updateVariantTitle(selectedVariant);
  updateVariantPrice(selectedVariant);

  attachBuyButtonListeners(product);
  attachOnVariantSelectListeners(product);

  attachQuantityIncrementListeners(product);
  attachQuantityDecrementListeners(product);

  updateCartTabButton();

  attachCheckoutButtonListeners();

  closeCart();

};


//
// Generate Selectors
//
const createProductOptions = product => {
  var elements = product.options.map(option => {
    return '<select name="' + option.name + '">' + option.values.map(value => {
      return '<option value="' + value + '">' + value + '</option>';
    }) + '</select>';
  });

  return elements;
};


//
// Updates product title
//
const updateProductTitle = function(title) {
  $('#buy-button-1 .product-title').text(title);
};


//
// Updates product image
//
const updateVariantImage = function(image) {
  $('#buy-button-1 .variant-image').attr('src', image.src);
};


//
// Update product variant title
//
const updateVariantTitle = function(variant) {
  $('#buy-button-1 .variant-title').text(variant.title);
};


//
// Update product variant price
//
const updateVariantPrice = function(variant) {
  $('#buy-button-1 .variant-price').text('$' + variant.price);
};


//
// Attach and control listeners onto buy button
//
const attachBuyButtonListeners = function (product) {
  $('.buy-button').on('click', function (event) {
    event.preventDefault();
    var id = product.selectedVariant.id;
    addVariantToCart(product.selectedVariant, 1);
  });
};


//
// Increase product variant quantity in cart
//
const attachQuantityIncrementListeners = function(product) {
  $('.cart').on('click', '.quantity-increment', function() {
    var variantId = parseInt($(this).attr('data-variant-id'), 10);
    var variant = product.variants.filter(function (variant) {
      return (variant.id === variantId);
    })[0];

    $(this).closest('.cart-item').addClass('js-working');
    $(this).attr('disabled', 'disabled');

    addVariantToCart(variant, 1);
  });
};


//
// Decrease product variant quantity in cart
//
const attachQuantityDecrementListeners = function(product) {
  $('.cart').on('click', '.quantity-decrement', function() {
    var variantId = parseInt($(this).attr('data-variant-id'), 10);
    var variant = product.variants.filter(function (variant) {
      return (variant.id === variantId);
    })[0];

    $(this).closest('.cart-item').addClass('js-working');
    $(this).attr('disabled', 'disabled');

    addVariantToCart(variant, -1);
  });
};


//
// When product variants change ...
//
const attachOnVariantSelectListeners = function(product, element) {
  $('.variant-selectors').on('change', 'select', event => {

    var $element = $(event.target);
    var name = $element.attr('name');
    var value = $element.val();
    product.options.filter(function(option) {
      return option.name === name;
    })[0].selected = value;

    var selectedVariant = product.selectedVariant;
    var selectedVariantImage = product.selectedVariantImage;
    updateProductTitle(product.title);
    updateVariantImage(selectedVariantImage);
    updateVariantTitle(selectedVariant);
    updateVariantPrice(selectedVariant);

  });
};


//
// Render Cart Items
//
function renderCartItems() {

  var cart;
  var cartLineItemCount;
  var $cartItemContainer = $('.cart-item-container');
  var totalPrice = 0;

  $cartItemContainer.empty();
  var lineItemEmptyTemplate = $('#cart-item-template').html();
  var $cartLineItems = cart.lineItems.map(function (lineItem, index) {
    var $lineItemTemplate = $(lineItemEmptyTemplate);
    var itemImage = lineItem.image.src;
    $lineItemTemplate.find('.cart-item__img').css('background-image', 'url(' + itemImage + ')');
    $lineItemTemplate.find('.cart-item__title').text(lineItem.title);
    $lineItemTemplate.find('.cart-item__variant-title').text(lineItem.variant_title);
    $lineItemTemplate.find('.cart-item__price').text(formatAsMoney(lineItem.line_price));
    $lineItemTemplate.find('.cart-item__quantity').attr('value', lineItem.quantity);
    $lineItemTemplate.find('.quantity-decrement').attr('data-variant-id', lineItem.variant_id);
    $lineItemTemplate.find('.quantity-increment').attr('data-variant-id', lineItem.variant_id);

    if (cartLineItemCount < cart.lineItems.length && (index === cart.lineItems.length - 1)) {
      $lineItemTemplate.addClass('js-hidden');
      cartLineItemCount = cart.lineItems.length;
    }

    if (cartLineItemCount > cart.lineItems.length) {
      cartLineItemCount = cart.lineItems.length;
    }

    return $lineItemTemplate;
  });
  $cartItemContainer.append($cartLineItems);

  setTimeout(function () {
    $cartItemContainer.find('.js-hidden').removeClass('js-hidden');
  }, 0);

  $('.cart .pricing').text(formatAsMoney(cart.subtotal));
}


//
// Check any cart items are in local storage
//
const checkForLocalStorage = function() {

  if(localStorage.getItem('lastCartId')) {
    return true;
  }

};


//
// Fetch Cart
//
const fetchCart = function(shopify) {

  // Returns a promise
  let cartPromise = shopify.fetchCart(localStorage.getItem('lastCartId'));
  return cartPromise;

};


//
// Create Cart
//
const createCart = function(shopify) {

  // Returns a promise
  let cartPromise = shopify.createCart();
  return cartPromise;

};


//
// Render cart items
//
const renderCart = function() {

  var cartLineItemCount = data.lineItems.length;
  renderCartItems();

};


//
// Set cart items
//
const setCart = function() {

  localStorage.setItem('lastCartId', data.id);
  var cartLineItemCount = 0;

};


//
// Initialize storage
//
const initStorage = function(shopify) {

  var isLocalStorage = checkForLocalStorage();

  if(isLocalStorage) {
    onResponse(fetchCart, renderCart, responseError);

  } else {
    onResponse(createCart, setCart, responseError);

  }

};






//
//
//
// const shopify = onResponse(getShopifyCreds, shopifyInit, responseError);
//
//
//
// const productPromise = getProduct(shopify, '3984406662');
//
// onResponse(productPromise, updateProduct, responseError);
//
// initStorage(shopify);



const checkShopifyCredentials = function() {
  if(hasExistingCredentials()) {
    return;

  } else {
    getShopifyCreds().then((creds) => {
      localStorage.shopifyAPICredentials = creds;
    })
    .catch(err => {
      error();
    });
  }
};



const shopify = shopifyInit(localStorage.shopifyAPICredentials);


checkShopifyCredentials
  .then(function() {

    shopifyInit(localStorage.shopifyAPICredentials);

  });





//
// Check if Shopify instance exists
//
const hasShopify = function() {
  return localStorage.getItem('Shopify');
};



//
// Init Shopify
//
const initShopify = function() {
  if(hasShopify()) {
    return localStorage.getItem('Shopify');

  } else {
    getShopifyCreds().then((creds) => {
      return shopifyInit(creds);
    })
    .catch(err => {
      error();
    });
  }
};











// const Shopify = (function() {
//
//   let init = function() {};
//
//   return {
//     init: init
//   };
//
// })();
//
// export default Shopify;

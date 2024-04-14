import {createRouter, createWebHistory} from 'vue-router';
import {createPinia} from 'pinia';

import MasterView from './components/pages/Master';
import Categories from './components/pages/Categories';
import Category from './components/pages/Category';
import Product from './components/pages/Product';
import Merchant from './components/pages/Merchant.vue';
import Checkout from './components/pages/Checkout';
import CheckoutSuccess from "./components/pages/CheckoutSuccess";
import Logout from './components/pages/Logout';
import Test from './components/pages/Test';
import Contact from "./components/pages/Contact";
import Construction from "./components/pages/Construction";
import Login from "./components/pages/Login.vue";
import AppSelect from "./admin/components/AppSelect.vue";
import ProductSelect from "./admin/components/ProductSelect.vue";
import ListOrders from "./admin/components/Payments.vue";
import AddProduct from "./admin/components/AddProduct.vue";
import EditProduct from "./admin/components/EditProduct.vue";
import OrderTracking from "./admin/components/Shipments.vue";
import Featured from "./components/pages/Featured.vue";
import Tag from "./components/pages/Tag.vue";
import Landing_2 from "./components/pages/landing/Landing_2.vue";
import Privacy from "./components/pages/Privacy.vue";
import ProductSync from "./admin/components/ProductSync.vue";
import PricingManager from "./admin/components/PricingManager";
import AddOrder from "./admin/components/AddOrder";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/login',
            name: 'login',
            component: Login,
            meta: {
                title: "WALRUS COMMERCE - Login",
            },
            props: true,
        },
        {
            path: '/logout',
            name: 'logout',
            component: Logout,
            meta: {
                title: "WALRUS COMMERCE - Logged Out",
            },
            props: true,
        },

        {
            path: '/',
            name: 'landing',
            component: Landing_2,
            meta: {
                title: "WALRUS COMMERCE - Welcome.",
            },
            props: true,
        },
        // {
        //     path: '/',
        //     name: 'landing_two',
        //     component: Landing_2,
        //     meta: {
        //         title: "WALRUS COMMERCE - Tarot Decks, Oracle Cards, Divination Tools, and more",
        //     },
        //     props: true,
        // },
        {
            path: '/welcome',
            name: 'construction',
            component: Construction,
            meta: {
                title: "WALRUS COMMERCE - Site Under Construction",
            },
            props: true,
        },
        {
            path: '/categories',
            name: 'categories',
            component: Categories,
            meta: {
                title: "WALRUS COMMERCE - Categories"
            },
            props: true,
        },
        {
            path: '/featured',
            name: 'featured',
            component: Featured,
            meta: {
                title: "WALRUS COMMERCE - Featured Items"
            },
            props: true,
        },
        {
            path: '/products/tag/:slug',
            name: 'tag',
            component: Tag,
            meta: {
                title: "WALRUS COMMERCE - Tag - ",
            },
            props: true,
        },
        {
            path: '/category/:slug',
            name: 'category',
            component: Category,
            meta: {
                title: "WALRUS COMMERCE - ",
            },
            props: true,
        },
        {
            path: '/product/:slug/:sku?',
            name: 'product',
            component: Product,
            meta: {
                title: "WALRUS COMMERCE - ",
            },
            props: true,
        },
        {
            path: '/merchant/:slug',
            name: 'merchant',
            component: Merchant,
            meta: {
                title: "WALRUS COMMERCE - Profile - ",
            },
            props: true,
        },
        {
            path: '/checkout',
            name: 'checkout',
            component: Checkout,
            meta: {
                title: "WALRUS COMMERCE - Checkout",
            },
            props: true,
        },
        {
            path: '/checkout_success/',
            name: 'checkout_success',
            component: CheckoutSuccess,
            meta: {
                title: "WALRUS COMMERCE - Order Placed Successfully",
            },
            props: true,
        },
        {
            path: '/contact',
            name: 'contact',
            component: Contact,
            meta: {
                title: "WALRUS COMMERCE - Contact Us",
            },
            props: true,
        },
        {
            path: '/privacy',
            name: 'privacy',
            component: Privacy,
            meta: {
                title: "WALRUS COMMERCE - Privacy Policy",
            },
            props: true,
        },
        {
            path: '/terms_of_use',
            name: 'terms_of_use',
            component: Privacy,
            meta: {
                title: "WALRUS COMMERCE - Terms of Use",
            },
            props: true,
        },
        {
            path: '/test',
            name: 'test',
            component: Test,
            props: true,
        },
        {
            path: '/admin',
            name: 'admin',
            component: AppSelect,
            meta: {
                title: "WALRUS COMMERCE - Admin Dashboard",
            },
            props: true,
        },
        {
            path: '/admin/pricing_manager',
            name: 'pricing_manager',
            component: PricingManager,
            meta: {
                title: "WALRUS COMMERCE - Pricing Manager",
            },
            props: true,
        },
        {
            path: '/admin/manage_products',
            name: 'manage_products',
            component: ProductSelect,
            meta: {
                title: "WALRUS COMMERCE - Manage Products",
            },
            props: true,
        },
        {
            path: '/admin/my_payments',
            name: 'my_payments',
            component: ListOrders,
            meta: {
                title: "WALRUS COMMERCE - All Orders",
            },
            props: true,
        },
        {
            path: '/admin/add_product',
            name: 'add_product',
            component: AddProduct,
            meta: {
                title: "WALRUS COMMERCE - Add Product",
            },
            props: true,
        },
        {
            path: '/admin/sync_products',
            name: 'sync_products',
            component: ProductSync,
            meta: {
                title: "WALRUS COMMERCE - Synchronize Products",
            },
            props: true,
        },
        {
            path: '/admin/edit_product/:product',
            name: 'edit_product',
            component: EditProduct,
            meta: {
                title: "WALRUS COMMERCE - Edit Product - ",
            },
            props: true,
        },
        {
            path: '/admin/track_orders',
            name: 'track_orders',
            component: OrderTracking,
            meta: {
                title: "WALRUS COMMERCE - Order Tracking",
            },
            props: true,
        },
        {
            path: '/admin/add_order',
            name: 'add_order',
            component: AddOrder,
            meta: {
                title: "WALRUS COMMERCE - New Order",
            },
            props: true,
        },
    ],
});

function capitalizeSlug(title, slug) {
    let capitalized = title + " ";
    slug.split("-").forEach(w => {
        capitalized += w[0].toUpperCase() + w.slice(1, w.length) + " ";
    });
    return capitalized.trimRight();
}

router.afterEach((to, from) => {
    Vue.nextTick(() => {
        if (to.params.slug) {
            document.title = capitalizeSlug(to.meta.title, to.params.slug);
        } else {
            document.title = to.meta.title;
        }
    });
});

Vue.createApp(MasterView)
    .use(router)
    .use(createPinia())
    .mount("#app");

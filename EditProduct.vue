<template>
    <admin-skeleton ref="pageSkeleton"></admin-skeleton>

    <div class="container">
        <div v-if="product_data" class="edit-product-container primary-container">
            <div class="page-full-heading">
                <h4 v-text="'Edit Product - ' + product_data.title"></h4>
            </div>
            <div class="edit-product">
                <custom-input
                    v-for="(data, key) in product_fields"
                    v-model="product_data[key]"
                    :inputType="data.type"
                    :options="categories ? categories : []"
                    :propertyKey="key"
                    :initialValue="product_data[key]"
                    :requiredField="data.required"
                    :extra="data.extra"
                    :displayFiles="data.displayFiles"
                    :newFiles="data.newFiles"
                    :files="data.type === 'file' && product_data['gallery_images'] ? product_data['gallery_images'][key] : []"
                    @set-value="setInputValue">
                </custom-input>
                <br/>
                <div @click="updateProduct" class="action-button purchase-button">
                    <p>Update Product</p>
                </div>
                <p v-cloak v-if="response.code" v-text="response.message" class="response-message"></p>
            </div>
        </div>
    </div>
</template>

<script>
import CustomInput from "../../components/generic/CustomInput.vue";
import AdminSkeleton from "./Skeleton.vue";

export default {
    name: "EditProduct",
    props: {
        product: String,
    },
    components: {
        "admin-skeleton": AdminSkeleton,
        "custom-input": CustomInput,
    },
    created() {
        this.getProduct(this.product);
        this.getCategories();
    },
    watch: {
        product_status: {
            handler(oldval, newval) {
                if (newval.files_uploaded.length === newval.file_count) {
                    this.submitProductData();
                }
            },
            deep: true,
        },
    },
    data() {
        return {
            product_fields: {
                full_name: {
                    value: "",
                    required: false,
                    type: 'text',
                },
                short_name: {
                    value: "",
                    required: true,
                    type: 'text',
                },
                slug: {
                    value: null,
                    required: true,
                    type: 'text',
                },
                creator: {
                    value: null,
                    required: false,
                    type: 'text',
                },
                description: {
                    value: null,
                    required: true,
                    type: 'textarea',
                },
                primary_image: {
                    value: null,
                    required: false,
                    type: 'file',
                    displayFiles: true,
                    newFiles: {
                        value: null,
                        required: false,
                        type: 'file',
                    }
                },
                secondary_image: {
                    value: null,
                    required: false,
                    type: 'file',
                    displayFiles: true,
                    newFiles: {
                        value: null,
                        required: false,
                        type: 'file',
                    }
                },
                additional_image: {
                    value: null,
                    required: false,
                    type: 'file',
                    displayFiles: true,
                    extra: {
                        multiple: true,
                    },
                    newFiles: {
                        value: null,
                        required: false,
                        type: 'file',
                    }
                },
                normal_price: {
                    value: null,
                    required: true,
                    type: 'text',
                },
                sale_price: {
                    value: null,
                    required: false,
                    type: 'text',
                },
                stripe_id: {
                    value: "",
                    required: true,
                    type: 'text',
                },
                published: {
                    value: "",
                    required: true,
                    type: 'checkbox',
                },
                sold_out: {
                    value: "",
                    required: true,
                    type: 'checkbox',
                },
                category: {
                    value: null,
                    required: true,
                    type: 'select'
                },
                // released_on: {
                //     value: null,
                //     required: false,
                //     type: 'text',
                // },
            },
            product_data: {},
            product_status: {
                files_uploaded: [],
                file_count: 0,
                submitting: false,
                uploading: false,
                published: false,
            },
            response: {
                message: null,
                code: null,
            },
            categories: null,
        }
    },
    methods: {
        getProduct(product) {
            let self = this;
            axios.get('/api/get_item/' + product, {}).then((response) => {
                self.product_data = response.data.item;
            }).catch(() => {
                // console.log('failed');
            });
        },
        async updateProduct() {
            let self = this;
            let types = ['additional_image', 'primary_image', 'secondary_image'];
            let uploads = [];

            self.product_status.file_count = 0;
            self.product_status.uploading = true;
            types.forEach(type => {
                self.product_data['gallery_images'][type].forEach((image) => {
                    console.log(image instanceof File);
                    if (image instanceof File) {
                        uploads.push([image, type]);
                        self.product_status.file_count++;
                    }
                });
            });
            self.product_status.uploading = false;
            self.uploadFiles(uploads);
        },
        async submitProductData() {
            let self = this;

            if (this.product_status.uploading) {
                return false;
            }

            clearInterval(self.product_status.submitting);
            self.product_status.uploading = false;

            axios.post('/api/admin/update_existing_product', {
                product_data: self.product_data,
                product_status: self.product_status
            }).then((response) => {
                self.product_status.submitting = false;
            }).catch((error) => {
                console.log(error);
            });
        },
        uploadFiles(uploads) {
            let self = this;
            self.product_status.files_uploaded = [];

            if (uploads && uploads.length > 0) {
                for (let i = 0; i < uploads.length; i++) {
                    let upload = uploads[i];
                    self.uploadFile(upload[0], upload[1]);
                }
            }
            return true;
        },
        async uploadFile(data, type) {
            let self = this;
            let formData = new FormData();

            formData.append(type, data);
            axios.post('/api/admin/upload_product_images/' + type,
                formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            ).then(function (response) {
                self.product_status.files_uploaded.push({
                    file: response.data.result_urls[0],
                    uploaded: true,
                });
            }).catch(() => {
                console.log('error uploading image');
            });
        },
        getCategories() {
            let self = this;
            axios.get('/api/get_categories', {}).then((response) => {
                self.categories = response.data.categories;
            }).catch(() => {
            });
        },
        setInputValue(v) {
            if (v['type'] === 'file') {
                if (v['newFile']) {
                    this.product_data['gallery_images'][v['key']].push((v['data'][0]));

                } else {
                    this.product_data[v['key']] = v['data'];
                }
            } else {
                this.product_data[v['key']] = v['value'];
            }
        },
    }
}
</script>

<style scoped>
.edit-product-container {
    padding: 90px 20px;
}

.edit-product-container > .col-lg-6:first-of-type {
    background-color: #DDD;
}

</style>

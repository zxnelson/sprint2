<template>
    <app-layout :title="product.name">
        <secondary-header>
            <template #breadcrumbs>
                <icon name="angle-right" class="w-4 h-4 fill-current"></icon>
                <Link :href="route('shop.index')" class="text-gray-700 transition hover:text-yellow-500">Shop</Link>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
                <icon name="angle-right" class="w-4 h-4 fill-current"></icon>
                <span>{{ product.name }}</span>
            </template>
            <template #search>
                <auto-complete></auto-complete>
            </template>
        </secondary-header>
        <div class="max-w-7xl mx-auto px-4 py-4 sm:flex sm:space-x-4 sm:px-6 lg:px-8">

        


<div class="flex flex-col flex-1 sm:border-r">
    <div class="relative border-2 overflow-hidden cursor-zoom-in h-full">
        <div id="img-container" class="w-full h-full">
            <img id="current-img" :src="'/storage/'+currentImg" :alt="product.name" class="w-full h-full object-cover origin-center">
        </div>





          <!-- Botón de ícono de cámara -->
    <a @click="openModal(product.archivo)" 
       class="absolute bottom-4 right-4 bg-white p-2 rounded-full shadow-md hover:bg-gray-100 focus:outline-none flex items-center justify-center">
      <i class="fas fa-camera text-gray-700 text-2xl"></i>
    </a>

   <!-- Modal -->
<div v-if="isModalOpen" 
     class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
  <div class="bg-white w-11/12 md:w-3/4 lg:w-1/2 p-4 rounded-lg shadow-lg relative">
    <button @click="closeModal" 
            class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
      <i class="fas fa-times"></i>
    </button>
    <iframe :src="modalUrl" 
            class="w-full h-96 rounded" frameborder="0" 
            style="overflow: hidden; width: 100%; height: 100%;" 
            scrolling="no"></iframe>
  </div>
</div>





        
    </div>
    <div class="mt-6" v-if="product.alt_images">
        <Carousel :settings="settings" :breakpoints="breakpoints">
            <Slide v-for="(image, index) in slides" :key="index" class="cursor-pointer border-2 border-black hover:border-blue-600" :class="{ selected: index === isActive, 'border-red-600': index === isActive }" @click.prevent="changeCurrentImage(image, index)">
                <div class="carousel__item flex w-full h-full">
                    <img :src="'/storage/'+image" class="object-cover" :class="{ 'opacity-50': index !== isActive  }">
                </div>
            </Slide>
            <template #addons>
                <Navigation />
            </template>
        </Carousel>
    </div>
</div>




            <div class="flex-1 space-y-6 my-4 sm:mt-0 sm:border-l sm:pl-4">
                <form @submit.prevent="submit">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-semibold capitalize italic">{{ product.name }}</h2>
                        <div class="text-xl capitalize italic">
                            <span>
                                Precio:
                            </span>
                            <span>
                                {{ $filters.formatCurrency(product.price) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex space-x-4 mt-4">
                        <p class="text-xl capitalize">
                            {{ product.details }}
                        </p>
                    </div>
                    <div class="flex space-x-4 mt-4">
                        <span class="text-xl capitalize">
                            Codigo:
                        </span>
                        <p class="text-xl capitalize">
                            {{ product.product_code }}
                        </p>
                    </div>

                    <div class="mt-4">
                        <template v-if="product.quantity <= 0">
                            <div class="mt-4">
                                <span class="text-2xl text-red-600 font-semibold italic uppercase">
                                    Agotado
                                </span>
                            </div>
                        </template>
                        <template v-else-if="product.quantity <= 5">
                            <div class="mt-4">
                                <span class="text-2xl text-yellow-600 font-semibold italic uppercase">
                                    Solo quedan unos pocos
                                </span>
                            </div>
                            <div class="flex items-center">
                                <label for="quantity" class="flex-1 text-xl capitalize">Cantidad:</label>
                                <select class="flex-1 w-full border bg-white rounded px-3 py-1 outline-none" tabindex="1" v-model="form.quantity">
                                    <option :value="qty" :selected="qty === quantity" v-for="(qty, index) in product.quantity" :key="index">{{ qty }}</option>
                                </select>
                            </div>
                        </template>
                        <template v-else>
                            <div class="flex items-center">
                                <label for="quantity" class="flex-1 text-xl capitalize">Cantidad:</label>
                                <select class="flex-1 w-full border bg-white rounded px-3 py-1 outline-none" tabindex="1" v-model="form.quantity">
                                    <option :value="qty" :selected="qty === quantity" v-for="(qty, index) in product.quantity" :key="index">{{ qty }}</option>
                                </select>
                            </div>
                        </template>
                    </div>
                    <div class="text-center mt-4" v-if="product.quantity > 0">
                        <gray-button as="submit" class="text-sm">
                            <span>añadir al carrito</span>
                        </gray-button>
                    </div>
                </form>
                <div class="flex flex-col divide-y">
                    <div>
                        <button type="button" class="flex justify-between items-center bg-gray-300 rounded-t px-4 py-4 w-full transition hover:text-white hover:bg-gray-700 sm:px-6 lg:px-8" @click.prevent="openDescription = !openDescription">
                            <span>Descripcion del producto</span>
                            <icon name="angle-down" class="w-5 h-5 text-yellow-500 fill-current" v-if="openDescription"></icon>
                            <icon name="angle-left" class="w-5 h-5 text-yellow-500 fill-current" v-else></icon>
                        </button>
                        <div class="bg-gray-50 px-4 py-4 sm:px-6 lg:px-8" v-if="openDescription">
                            <p>
                                {{ product.description }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="flex justify-between items-center bg-gray-300 rounded-t px-4 py-4 w-full transition hover:text-white hover:bg-gray-700 sm:px-6 lg:px-8" @click.prevent="openFeatures = !openFeatures">
                            <span>Características del producto</span>
                            <icon name="angle-down" class="w-5 h-5 text-yellow-500 fill-current" v-if="openFeatures"></icon>
                            <icon name="angle-left" class="w-5 h-5 text-yellow-500 fill-current" v-else></icon>
                        </button>
                        <div class="bg-gray-50 px-4 py-4 sm:px-6 lg:px-8" v-if="openFeatures">
                            <p>
                                {{ product.description }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="flex justify-between items-center bg-gray-300 rounded-t px-4 py-4 w-full transition hover:text-white hover:bg-gray-700 sm:px-6 lg:px-8" @click.prevent="openReturn = !openReturn">
                            <span>Política de devoluciones</span>
                            <icon name="angle-down" class="w-5 h-5 text-yellow-500 fill-current" v-if="openReturn"></icon>
                            <icon name="angle-left" class="w-5 h-5 text-yellow-500 fill-current" v-else></icon>
                        </button>
                        <div class="bg-gray-50 px-4 py-4 sm:px-6 lg:px-8" v-if="openReturn">
                            <p>
                                ¡No te preocupes por las devoluciones, te enviaremos uno nuevo!
                            </p>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="flex justify-between items-center bg-gray-300 rounded-t px-4 py-4 w-full transition hover:text-white hover:bg-gray-700 sm:px-6 lg:px-8" @click.prevent="openReviews = !openReviews">
                            <span>Reseñas</span>
                            <icon name="angle-down" class="w-5 h-5 text-yellow-500 fill-current" v-if="openReviews"></icon>
                            <icon name="angle-left" class="w-5 h-5 text-yellow-500 fill-current" v-else></icon>
                        </button>
                        <div class="bg-gray-50 px-4 py-4 sm:px-6 lg:px-8" v-if="openReviews">
                            <p>
                                ¡Es fantástico!
                            </p>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <p>Sugerido según su búsqueda</p>
                </div>
                <div class="flex space-x-4">
                    <Link :href="route('shop.show', item.slug)" class="flex border border-black w-1/4 h-24" v-for="(item, index) in similarProducts"  :key="index">
                        <img :src="'/storage/'+item.main_image" :alt="item.name" class="w-full object-cover">
                    </Link>
                </div>
            </div>
        </div>
    </app-layout>
</template>



<script>
import { defineComponent } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'
import { Carousel, Slide, Navigation } from 'vue3-carousel';
import AppLayout from '@/Layouts/AppLayout'
import AutoComplete from '@/Components/Search/AutoComplete'
import GrayButton from '@/Components/Buttons/GrayButton'
import SecondaryHeader from '@/Components/SecondaryHeader'

export default defineComponent({
    props: ['product', 'similarProducts'],
    components: {
        Link,
        AppLayout,
        AutoComplete,
        GrayButton,
        SecondaryHeader,
        Carousel,
        Slide,
        Navigation,
    },
    data() {
        return {
            currentImg: this.product.main_image,
            isActive: 0,
            selected: false,
            openDescription: false,
            openFeatures: false,
            openReturn: false,
            openReviews: false,
            quantity: 1,
            isModalOpen: false,
            modalUrl: '',
            form: this.$inertia.form({
                id: this.product.id,
                name: this.product.name,
                price: this.product.price,
                product_code: this.product.product_code,
                details: this.product.details,
                main_image: this.product.main_image,
                alt_images: this.product.alt_images,
                slug: this.product.slug,
                quantity: 1,
                totalQty: this.product.quantity,
            }),
            slides: this.product.alt_images,
            settings: {
                itemsToShow: 1,
                snapAlign: 'center',
            },
            breakpoints: {
                700: {
                    itemsToShow: 3.5,
                    snapAlign: 'center',
                },
                1024: {
                    itemsToShow: 5,
                    snapAlign: 'start',
                },
            },
        }
    },
    mounted() {
        this.zoomImage()
    },
    methods: {
        submit() {
            this.form.post(this.route('cart.store', this.form), {
                preserveScroll: true,
                onSuccess: () => {
                    Toast.fire({
                        icon: 'success',
                        title: `${this.form.name} se ha agregado exitosamente a su carrito!`
                    })
                }
            })
        },
        changeCurrentImage(image, index) {
            if (image) {
                this.currentImg = image
                this.isActive = index
                this.selected = false
            } else {
                this.currentImg = this.product.main_image
                if (this.isActive = index) {
                    this.selected = false
                } else {
                    this.selected = true
                }
            }
        },
        zoomImage() {
            let container = document.querySelector('#img-container')
            let img = document.querySelector('#current-img')
            container.addEventListener("mousemove", (e) => {
                let x = e.clientX - e.target.offsetLeft
                let y = e.clientY - e.target.offsetTop
                img.style.transformOrigin = `${x}px ${y}px`
                img.style.transform = "scale(3)"
            })
            container.addEventListener("mouseleave", () => {
                img.style.transformOrigin = "center"
                img.style.transform = "scale(1)"
            })
        },
        openModal(url) {
            this.modalUrl = url;
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
        },
    }
})
</script>

<template>
    <div class="shadow-md rounded sm:my-2">
        <div class="flex flex-col items-center space-y-4 py-6 bg-gray-700">
            <div class="flex space-x-4">
                <span class="text-white">
                    Total del pedido (antes de impuestos y descuentos)
                </span>
                <span class="text-yellow-500">{{ $filters.formatCurrency(subtotal) }}</span>
            </div>
            <div>
                <yellow-button :href="route('checkout.index')" as="href" class="text-sm">Pagar de forma segura</yellow-button>
            </div>
        </div>
        <div class="bg-gray-300 px-4 py-6">
            <div>
                <span class="px-4">Resumen del pedido</span>
                <div class="flex justify-between bg-white px-4 py-2 mt-4">
                    <span>Subtotal de artículos ({{ $page.props.cartCount }})</span>
                    <span>{{ $filters.formatCurrency(newSubtotal) }}</span>
                </div>
                <div class="flex justify-between px-4 mt-4">
                    <span>Envío</span>
                    <span>Gratis</span>
                </div>
                <div class="flex justify-between px-4 mt-4" v-if="code">
                    <span>Código de descuento ({{ code }})</span>
                    <form @submit.prevent="deleteCoupon">
                        <span>-{{ $filters.formatCurrency(discount) }}</span>
                        <button type="submit" class="text-red-600 ml-2">X</button>
                    </form>
                </div>
                <div class="flex justify-between px-4 mt-4">
                    <span>Impuesto estimado</span>
                    <span>{{ $filters.formatCurrency(tax) }}</span>
                </div>
                <div class="bg-white px-4 py-2 mt-4">
                    <div class="flex justify-between">
                        <span>Total del pedido</span>
                        <span>{{ $filters.formatCurrency(total) }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span>({{ taxRate }}% tasa de impuesto)</span>
                        <span></span>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <yellow-button :href="route('checkout.index')" as="href" class="text-sm">Pagar de forma segura</yellow-button>
                </div>
            </div>
            <div class="text-center mt-4">
                <Link :href="route('shop.index')" class="underline hover:text-red-700 transition">Continuar comprando</Link>
            </div>
        </div>
    </div>
    <div class="flex flex-col items-center bg-gray-300 shadow-md rounded mt-4 py-6" v-if="!code">
        <span class="text-2xl font-semibold">Promoción</span>
        <form @submit.prevent="addCoupon" class="w-full">
            <div class="bg-gray-300 px-4">
                <div>
                    <div class="flex flex-col items-center bg-white px-4 py-4 mt-2">
                        <input type="text" class="w-full" placeholder="Introduce el código promocional aquí" v-model="form.coupon_code">
                        <span class="text-md text-red-600 mt-2" v-if="$page.props.errors.message">
                            {{ $page.props.errors.message }}
                        </span>
                    </div>
                    <div class="text-center mt-4">
                        <gray-button as="button" class="text-sm">
                            Aplicar
                        </gray-button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import { defineComponent } from 'vue'
    import { Link } from '@inertiajs/inertia-vue3';
    import GrayButton from '@/Components/Buttons/GrayButton'
    import YellowButton from '@/Components/Buttons/YellowButton'
    export default defineComponent({
        props: ['taxRate', 'subtotal', 'tax', 'total', 'newSubtotal', 'code', 'discount'],
        components: {
            Link,
            GrayButton,
            YellowButton,
        },
        data() {
            return {
                form: this.$inertia.form({
                    coupon_code: '',
                })
            }
        },
        methods: {
            addCoupon() {
                this.form.post(this.route('coupon.store'), {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.form.reset()
                        Toast.fire({
                            icon: 'success',
                            title: '¡El cupón ha sido añadido!'
                        })
                    }
                })
            },
            deleteCoupon() {
                this.form.delete(this.route('coupon.destroy'), {
                    preserveScroll: true,
                    onSuccess: () => {
                        Toast.fire({
                            icon: 'success',
                            title: '¡El cupón ha sido eliminado!'
                        })
                    }
                })
            }
        }
    })
</script>


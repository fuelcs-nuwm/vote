<template>
    <div class="content container py-3 flex-grow-1">
        <h2>Користувач</h2>
        <p class="breadcrumb">
            <router-link tag="span" :to="{ name: 'admin.events'}">
                <b class="breadcrumb-item link">Налаштування подій</b>
            </router-link>
            <span>&nbsp;- Користувач</span></p>
        <h5>Подія: {{ event && event.title }}</h5>

        <form @submit.prevent="storeCustomer">
            <div class="input-group mb-3">

                <input type="text" class="form-control" placeholder="Новий користувач" v-model="newCustomer.email">
                <div class="input-group-append">
                    <span type="submit" class="input-group-text" @click="storeCustomer">Додати користувача</span>
                </div>
            </div>
        </form>
        <div
            v-if="!$v.newCustomer.email.required && $v.newCustomer.email.$dirty "
            class="text-danger"
        >Введіть назву
        </div>
        <div
            v-if="!$v.newCustomer.email.email && $v.newCustomer.email.$dirty "
            class="text-danger"
        >Невірний email
        </div>

        <hr>

        <div v-for="customer in customers">
            <div v-if="editId == -1" class="input-group mb-3">
                <input
                    type="text" class="form-control"
                    placeholder="Нова подія"
                    v-model="customer.email"
                    @click="editCustomer(customer)"
                    readonly
                >
                <div class="input-group-append">
                    <span class="input-group-text" @click="deleteCustomer(customer.id)">Видалити користувача</span>
                </div>
            </div>
            <div v-if="editId == customer.id" class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Новий користувач" v-model="editedCustomer.email">
                <div class="input-group-append">
                    <span class="input-group-text" @click="updateCustomer">Редагувати</span>
                </div>
                <div class="input-group-append">
                    <span class="input-group-text" @click="cancelEdit">Скасувати</span>
                </div>
            </div>
            <div
                v-if="editId == customer.id && !$v.editedCustomer.email.required && $v.editedCustomer.email.$dirty"
                class="text-danger"
            >Введіть назву
            </div>
            <div
                v-if="!$v.editedCustomer.email.email && $v.editedCustomer.email.$dirty "
                class="text-danger"
            >Невірний email
            </div>
        </div>
    </div>
</template>

<script>
import {mapMutations} from "vuex";
import {clone as _clone} from 'lodash';
import {
    email,
    required,
    maxLength
} from "vuelidate/lib/validators";

export default {
    data() {
        return {
            eventId: null,
            event: null,
            editId: -1,
            customers: [],
            newCustomer: {
                email: ""
            },
            editedCustomer: {
                email: ""
            }
        };
    },
    validations: {
        newCustomer: {
            email: {
                email,
                required,
                maxLength: maxLength(255),
            },
        },
        editedCustomer: {
            email: {
                email,
                required,
                maxLength: maxLength(255),
            },
        }
    },
    mounted() {
        this.init();
    },
    methods:{
        ...mapMutations({
            setIsShowSpinner: 'setIsShowSpinner'
        }),
        init () {
            this.eventId = this.$route.params.id;
            this.setIsShowSpinner(true);
            this.getEvent(this.eventId);
            this.getCustomers(this.eventId);
            this.isLoaded = true;
            this.setIsShowSpinner(false);
        },
        getEvent(eventId) {
            this.setIsShowSpinner(true);
            axios
                .get(`/events/${eventId}`)
                .then(response => {
                    console.log(response.data);
                    this.event = response.data.data;
                })
                .catch(error => {
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        getCustomers(eventId) {
            this.setIsShowSpinner(true);
            axios
                .get(`customers/events/${eventId}`)
                .then(response => {
                    console.log(response.data);
                    this.customers = response.data.data;
                })
                .catch(error => {
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        storeCustomer() {
            if (this.$v.newCustomer.email.$invalid) {
                this.$v.newCustomer.email.$touch();
                return;
            }
            this.setIsShowSpinner(true);
            axios
                .post(`customers`, {
                    email: this.newCustomer.email,
                    event_id: this.eventId,
                })
                .then(response => {
                    this.getCustomers(this.eventId);
                    this.newCustomer.email = "";
                })
                .catch(error => {
                    console.log(error.response)
                    let message = "";
                    error.response.data.message.email.forEach((text) => {
                        message += text;
                    });
                    alert (message);
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        editCustomer(customer) {
            this.editId = customer.id;
            this.editedCustomer =_clone(customer, true);
        },
        cancelEdit () {
            this.editId = -1;
            this.editedCustomer = null;
        },
        updateCustomer() {
            if (this.$v.editedCustomer.title.$invalid) {
                this.$v.editedCustomer.title.$touch();
                return;
            }

            this.setIsShowSpinner(true);
            axios
                .put(`customers/${this.editId}`, {
                    email: this.editedCustomer.email,
                })
                .then(response => {
                    this.getCustomers(this.eventId);
                })
                .catch(error => {

                })
                .then(() => {
                    this.setIsShowSpinner(false);
                    this.cancelEdit();
                });
        },
        deleteCustomer(customerId) {
            if (window.confirm("Видалити користувача?")) {
                this.setIsShowSpinner(true);
                axios
                    .delete(`/customers/${customerId}`)
                    .then(response => {
                        this.getCustomers(this.eventId);
                    })
                    .catch(error => {
                    })
                    .then(() => {
                        this.setIsShowSpinner(false);
                    });
            }
        },
    }
}
</script>

<style scoped lang="scss">

.breadcrumb-item.link {
    cursor: pointer;
}

.content {
    background-color: rgb(241, 241, 241);
}

</style>

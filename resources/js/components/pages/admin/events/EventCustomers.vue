<template>
    <div class="content container py-3 flex-grow-1">
        <h2>Учасники</h2>
        <p class="breadcrumb">
            <router-link tag="span" :to="{ name: 'admin.events'}">
                <b class="breadcrumb-item link">Налаштування подій</b>
            </router-link>
            <span>&nbsp;- Учасники</span></p>
        <h5>Подія: {{ event && event.title }}</h5>

        <form @submit.prevent="storeCustomer">
            <div class="input-group mb-3">

                <input type="text" class="form-control" placeholder="Введіть email" v-model="newCustomer.email">
                <div class="input-group-append">
                    <span type="submit" class="input-group-text" @click="storeCustomer">Додати учасника</span>
                </div>
            </div>
        </form>
        <div
            v-if="!$v.newCustomer.email.required && $v.newCustomer.email.$dirty "
            class="text-danger"
        >Введіть email
        </div>
        <div
            v-if="!$v.newCustomer.email.email && $v.newCustomer.email.$dirty "
            class="text-danger"
        >Невірний email
        </div>

        <div v-if="event" class="form-group mb-3">
            <label for="exampleFormControlSelect2">Виберіть групу учасника</label>
            <select multiple class="form-control" id="exampleFormControlSelect2" v-model="newCustomer.groupIds">
                <option v-for="group in event.groups" :value="group.id">{{ group.name }}</option>
            </select>
        </div>
        <div
            v-if="!$v.newCustomer.groupIds.required && $v.newCustomer.groupIds.$dirty "
            class="text-danger"
        >Виберіть групу
        </div>

        <hr>

        <div v-for="customer in customers">
            <div v-if="editId == -1" >
                <div class="input-group mb-3">
                    <input
                        type="text" class="form-control"
                        v-model="customer.email"
                        @click="editCustomer(customer)"
                        readonly
                    >
                    <div class="input-group-append">
                        <span class="input-group-text" @click="deleteCustomer(customer.id)">Видалити учасника</span>
                    </div>
                </div>
                <div class="alert alert-secondary">
                    <span v-for="group in customer.groups"> {{ group.name }}</span>
                </div>
            </div>

            <div v-if="editId == customer.id" >
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Введіть email"  v-model="editedCustomer.email">
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
                >Введіть email
                </div>
                <div
                    v-if="!$v.editedCustomer.email.email && $v.editedCustomer.email.$dirty "
                    class="text-danger"
                >Невірний email
                </div>
                <div v-if="event" class="form-group mb-3">
                    <label for="editFormControlSelect2">Виберіть групу учасника</label>
                    <select multiple class="form-control" id="editFormControlSelect2" v-model="editedCustomer.groupIds">
                        <option v-for="group in event.groups" :value="group.id">{{ group.name }}</option>
                    </select>
                </div>
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
                email: "",
                groupIds: [],
            },
            editedCustomer: {
                email: "",
                groupIds: []
            }
        };
    },
    validations: {
        newCustomer: {
            email: {
                email,
                required,
                maxLength: maxLength(100),
            },
            groupIds: {
                required,
            },
        },
        editedCustomer: {
            email: {
                email,
                required,
                maxLength: maxLength(100),
            },
            groupIds: {
                required,
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
            if (this.$v.newCustomer.email.$invalid || this.$v.newCustomer.groupIds.$invalid) {
                this.$v.newCustomer.email.$touch();
                this.$v.newCustomer.groupIds.$touch();
                return;
            }
            this.setIsShowSpinner(true);
            axios
                .post(`customers`, {
                    email: this.newCustomer.email,
                    event_id: this.eventId,
                    group_ids: this.newCustomer.groupIds
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
            let groupIds = [];
            this.editedCustomer.groups.forEach((group) => {
                groupIds.push(group.id);
            })
            this.editedCustomer.groupIds = groupIds;
        },
        cancelEdit () {
            this.editId = -1;
            this.editedCustomer = null;
        },
        updateCustomer() {
            if (this.$v.editedCustomer.email.$invalid) {
                this.$v.editedCustomer.email.$touch();
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

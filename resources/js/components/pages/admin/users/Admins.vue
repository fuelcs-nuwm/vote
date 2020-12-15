<template>
    <div class="content container py-3 flex-grow-1">
        <h2>Адміністратори</h2>

        <form @submit.prevent="storeUser">
            <div class="input-group mb-3">

                <input type="text" class="form-control" placeholder="Введіть email" v-model="newUser.email">
                <div class="input-group-append">
                    <span type="submit" class="input-group-text" @click="storeUser">Додати учасника</span>
                </div>
            </div>
        </form>
        <div
            v-if="!$v.newUser.email.required && $v.newUser.email.$dirty "
            class="text-danger"
        >Введіть email
        </div>
        <div
            v-if="!$v.newUser.email.email && $v.newUser.email.$dirty "
            class="text-danger"
        >Невірний email
        </div>

        <hr>

        <div v-for="user in users">
            <div v-if="editId == -1" >
                <div class="input-group mb-3">
                    <input
                        type="text" class="form-control"
                        v-model="user.email"
                        @click="editUser(user)"
                        readonly
                    >
                    <div class="input-group-append">
                        <span class="input-group-text" @click="deleteUser(user.id)">Видалити учасника</span>
                    </div>
                </div>
            </div>

            <div v-if="editId == user.id" >
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Введіть email"  v-model="editedUser.email">
                    <div class="input-group-append">
                        <span class="input-group-text" @click="updateUser">Редагувати</span>
                    </div>
                    <div class="input-group-append">
                        <span class="input-group-text" @click="cancelEdit">Скасувати</span>
                    </div>
                </div>
                <div
                    v-if="editId == user.id && !$v.editedUser.email.required && $v.editedUser.email.$dirty"
                    class="text-danger"
                >Введіть email
                </div>
                <div
                    v-if="!$v.editedUser.email.email && $v.editedUser.email.$dirty "
                    class="text-danger"
                >Невірний email
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
            users: [],
            newUser: {
                email: "",
            },
            editedUser: {
                email: "",
            }
        };
    },
    validations: {
        newUser: {
            email: {
                email,
                required,
                maxLength: maxLength(100),
            },
        },
        editedUser: {
            email: {
                email,
                required,
                maxLength: maxLength(100),
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
            this.setIsShowSpinner(true);
            this.getUsers();
        },
        getUsers() {
            this.setIsShowSpinner(true);
            axios
                .get(`admins`)
                .then(response => {
                    console.log(response.data);
                    this.users = response.data.data;
                })
                .catch(error => {
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        storeUser() {
            if (this.$v.newUser.email.$invalid) {
                this.$v.newUser.email.$touch();
                return;
            }
            this.setIsShowSpinner(true);
            axios
                .post(`admins`, {
                    email: this.newUser.email,
                })
                .then(response => {
                    this.getUsers();
                    this.newUser.email = "";
                })
                .catch(error => {
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
        editUser(user) {
            this.editId = user.id;
            this.editedUser =_clone(user, true);
        },
        cancelEdit () {
            this.editId = -1;
            this.editedUser = null;
        },
        updateUser() {
            if (this.$v.editedUser.email.$invalid) {
                this.$v.editedUser.email.$touch();
                return;
            }

            this.setIsShowSpinner(true);
            axios
                .put(`admins/${this.editId}`, {
                    email: this.editedUser.email,
                })
                .then(response => {
                    this.getUsers();
                })
                .catch(error => {

                })
                .then(() => {
                    this.setIsShowSpinner(false);
                    this.cancelEdit();
                });
        },
        deleteUser(userId) {
            if (window.confirm("Видалити користувача?")) {
                this.setIsShowSpinner(true);
                axios
                    .delete(`/admins/${userId}`)
                    .then(response => {
                        this.getUsers(this.eventId);
                    })
                    .catch(error => {
                        alert(error.response.data.message);
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

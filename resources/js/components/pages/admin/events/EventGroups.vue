<template>
    <div class="content container py-3 flex-grow-1">
        <h2>Група</h2>
        <p class="breadcrumb">
            <router-link tag="span" :to="{ name: 'admin.events'}">
                <b class="breadcrumb-item link">Налаштування подій</b>
            </router-link>
            <span>&nbsp;- Група</span></p>
        <h5>Подія: {{ event && event.title }}</h5>

        <form @submit.prevent="storeGroup">
            <div class="input-group mb-3">

                <textarea
                    type="text"
                    class="form-control"
                    placeholder="Нова група"
                    v-model="newGroup.name"
                    rows="3"
                ></textarea>
                <div class="input-group-append">
                    <span type="submit" class="input-group-text" @click="storeGroup">Додати групу</span>
                </div>
            </div>
        </form>
        <div
            v-if="!$v.newGroup.name.required && $v.newGroup.name.$dirty "
            class="text-danger"
        >Введіть назву
        </div>

        <hr>

        <div v-for="group in groups">
            <div v-if="editId == -1" class="input-group mb-3">
                <textarea
                    type="text" class="form-control"
                    v-model="group.name"
                    rows="3"
                    @click="editGroup(group)"
                    readonly
                ></textarea>
                <div class="input-group-append">
                    <span class="input-group-text" @click="deleteGroup(group.id)">Видалити групу</span>
                </div>
            </div>
            <div v-if="editId == group.id" class="input-group mb-3">
                <textarea
                    type="text"
                    class="form-control"
                    placeholder="Назва групи"
                    v-model="editedGroup.name"
                    rows="3"
                ></textarea>
                <div class="input-group-append">
                    <span class="input-group-text" @click="updateGroup">Редагувати</span>
                </div>
                <div class="input-group-append">
                    <span class="input-group-text" @click="cancelEdit">Скасувати</span>
                </div>
            </div>
            <div
                v-if="editId == group.id && !$v.editedGroup.name.required && $v.editedGroup.name.$dirty"
                class="text-danger"
            >Введіть назву
            </div>
        </div>
    </div>
</template>

<script>
import {mapMutations} from "vuex";
import {clone as _clone} from 'lodash';
import {
    required,
    maxLength
} from "vuelidate/lib/validators";

export default {
    data() {
        return {
            eventId: null,
            event: null,
            editId: -1,
            groups: [],
            newGroup: {
                name: ""
            },
            editedGroup: {
                name: ""
            }
        };
    },
    validations: {
        newGroup: {
            name: {
                required,
                maxLength: maxLength(5000),
            },
        },
        editedGroup: {
            name: {
                required,
                maxLength: maxLength(5000),
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
            this.getGroups(this.eventId);
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
        getGroups(eventId) {
            this.setIsShowSpinner(true);
            axios
                .get(`groups/events/${eventId}`)
                .then(response => {
                    console.log(response.data);
                    this.groups = response.data.data;
                })
                .catch(error => {
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        storeGroup() {
            if (this.$v.newGroup.name.$invalid) {
                this.$v.newGroup.name.$touch();
                return;
            }
            this.setIsShowSpinner(true);
            axios
                .post(`groups`, {
                    name: this.newGroup.name,
                    event_id: this.eventId,
                })
                .then(response => {
                    this.getGroups(this.eventId);
                })
                .catch(error => {

                })
                .then(() => {
                    this.newGroup.name = "";
                    this.setIsShowSpinner(false);
                });
        },
        editGroup(group) {
            this.editId = group.id;
            this.editedGroup =_clone(group, true);
        },
        cancelEdit () {
            this.editId = -1;
            this.editedGroup = null;
        },
        updateGroup() {
            if (this.$v.editedGroup.name.$invalid) {
                this.$v.editedGroup.name.$touch();
                return;
            }

            this.setIsShowSpinner(true);
            axios
                .put(`groups/${this.editId}`, {
                    name: this.editedGroup.name,
                })
                .then(response => {
                    this.getGroups(this.eventId);
                })
                .catch(error => {

                })
                .then(() => {
                    this.setIsShowSpinner(false);
                    this.cancelEdit();
                });
        },
        deleteGroup(groupId) {
            if (window.confirm("Видалити група?")) {
                this.setIsShowSpinner(true);
                axios
                    .delete(`/groups/${groupId}`)
                    .then(response => {
                        this.getGroups(this.eventId);
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

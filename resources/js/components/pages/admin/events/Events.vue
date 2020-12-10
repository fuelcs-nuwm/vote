<template>
    <div>
        <h2>Події</h2>

        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Нова подія" v-model="newEvent.title">
            <div class="input-group-append">
                <span class="input-group-text" @click="storeEvent">Додати подію</span>
            </div>
        </div>
        <div
            v-if="!$v.newEvent.title.required && $v.newEvent.title.$dirty "
            class="text-danger"
        >Введіть назву
        </div>

        <div v-for="event in events">
            <div v-if="editId == -1" class="input-group mb-3" @click="editEvent(event)">
                <input type="text" class="form-control" placeholder="Нова подія" v-model="event.title" readonly>
                <div class="input-group-append">
                    <span class="input-group-text" @click="deleteEvent(event.id)">Видалити подію</span>
                </div>
            </div>
            <div v-if="editId == event.id" class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Нова подія" v-model="editedEvent.title">
                <div class="input-group-append">
                    <span class="input-group-text" @click="updateEvent">Редагувати</span>
                </div>
                <div class="input-group-append">
                    <span class="input-group-text" @click="cancelEdit">Скасувати</span>
                </div>
            </div>
            <div
                v-if="editId == event.id && !$v.editedEvent.title.required && $v.editedEvent.title.$dirty"
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
            editId: -1,
            events: [],
            newEvent: {
                title: ""
            },
            editedEvent: {
                title: ""
            }
        };
    },
    validations: {
        newEvent: {
            title: {
                required,
                maxLength: maxLength(255),
            },
        },
        editedEvent: {
            title: {
                required,
                maxLength: maxLength(255),
            },
        }
    },
    mounted() {
        this.getEvents();
    },
    methods:{
        ...mapMutations({
            setIsShowSpinner: 'setIsShowSpinner'
        }),
        getEvents() {
            this.setIsShowSpinner(true);
            axios
                .get(`/events`)
                .then(response => {
                    console.log(response.data);
                    this.events = response.data.data;
                })
                .catch(error => {
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        storeEvent() {
            if (this.$v.newEvent.title.$invalid) {
                this.$v.newEvent.title.$touch();
                return;
            }
            this.setIsShowSpinner(true);
            axios
                .post(`events`, {
                    title: this.newEvent.title,
                })
                .then(response => {
                    this.getEvents();
                })
                .catch(error => {

                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
        editEvent(event) {
            this.editId = event.id;
            this.editedEvent =_clone(event, true);
        },
        cancelEdit () {
            this.editId = -1;
            this.editedEvent = nul;
        },
        updateEvent() {
            if (this.$v.editedEvent.title.$invalid) {
                this.$v.editedEvent.title.$touch();
                return;
            }

            this.setIsShowSpinner(true);
            axios
                .put(`events/${this.editId}`, {
                    title: this.editedEvent.title,
                })
                .then(response => {
                    this.getEvents();
                })
                .catch(error => {

                })
                .then(() => {
                    this.setIsShowSpinner(false);
                    this.cancelEdit();
                });
        },
        deleteEvent(eventId) {
            this.setIsShowSpinner(true);
            axios
                .delete(`/events/${eventId}`)
                .then(response => {
                    this.getEvents();
                })
                .catch(error => {
                })
                .then(() => {
                    this.setIsShowSpinner(false);
                });
        },
    }
}
</script>

<style scoped>

</style>

<template>
    <div class="row">
        <div class="col">
            <q-file
                v-model="documents"
                label="Нажмите для выбора"
                outlined
                use-chips
                multiple
                append
                style="max-width: 300px"
                @input="uploadNewDocuments"
            />
        </div>
        <div class="col">
            <q-table
                :data="orderDocuments"
                :columns="documentsColumns"
                no-data-label="К данной консультации еще не прикреплено ни одного файла"
            >
                <template #body-cell-fileName="props">
                    <q-td :props="props">
                        <a
                            :href="createDownloadUrl(props.row.id)"
                            @click.prevent="createWindow(createDownloadUrl(props.row.id))"
                        >
                            {{props.row.fileName}}
                        </a>
                    </q-td>
                </template>
                <template #body-cell-delete="props">
                    <q-td :props="props">
                        <div class="row items-start q-gutter-xs">
                            <q-btn
                                dense
                                flat
                                color="negative"
                                icon="delete"
                                @click="deleteDocument(props.row.id)"
                            />
                        </div>
                    </q-td>
                </template>
            </q-table>
        </div>
    </div>
</template>

<script>
import {mapActions} from 'vuex';
import {apiConstants as adminApi} from '../../../admin/js/api';
import {apiConstants as frontApi} from '../../../front/js/api';

export default {
    name: 'OrderDocuments',
    props: {
        orderDocuments: {
            type: Array,
            required: true,
        },
        orderId: {
            type: String,
            required: true,
        },
        fromAdmin: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            documents: null,
            documentsColumns: [
                {name: 'fileName', align: 'left', label: 'Наименование', field: 'fileName'},
                {name: 'createTime', align: 'left', label: 'Время добавления', field: 'createTime'},
                {name: 'delete', align: 'left', label: 'Удалить', field: 'delete'},
            ],
        };
    },
    methods: {
        ...mapActions({
            uploadOrderDocuments: 'order/uploadOrderDocuments',
            deleteOrderDocument: 'order/deleteOrderDocument',
        }),
        async uploadNewDocuments() {
            const formData = new FormData();
            this.documents.forEach((document, index) => formData.append(`documentType[${index}]`, document));
            try {
                await this.uploadOrderDocuments({id: this.orderId, payload: formData});
                this.documents = null;
                this.$emit('update:order');
            } catch (error) {
                this.documents = null;
                console.log(error);
            }
        },
        async deleteDocument(id) {
            try {
                await this.deleteOrderDocument({id});
                this.$emit('update:order');
            } catch (error) {
                console.log(error);
            }
        },
        createDownloadUrl(id) {
            const downloadLink = this.fromAdmin ? adminApi.ORDER.GET_DOC(id) : frontApi.ORDER.GET_DOC(id);
            const {origin} = window.location;

            return `${origin}${downloadLink}`;
        },
        async createWindow(url) {
            const params = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,
                width=0,height=0,left=-1000,top=-1000`;

            window.open(url, 'document', params);
        },
    },
};
</script>

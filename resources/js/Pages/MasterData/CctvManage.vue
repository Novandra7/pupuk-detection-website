<template>
    <Head title="Cctv Management"/>
    <MainLayout title="Cctv Management">
        <template #header-action>
            <BsButton type="primary" icon="plus" @click="addCctvAction" v-if="can('cctv.create')">Add Cctv</BsButton>
        </template>
        <div class="flex flex-row">
            <DxDataGrid ref="datagridRef" :data-source="dataSource" key="id" @selection-changed="onSelectionChanged" @cell-dbl-click="editCctvAction($event.data)">
                <DxColumn data-field="warehouse.warehouse_name" caption="Warehouse" :allowHeaderFiltering="false" />
                <DxColumn data-field="source_name" caption="Source Name" :allowHeaderFiltering="false" />
                <DxColumn data-field="url_streaming" caption="Url Streaming" :allowHeaderFiltering="false" />
                <DxColumn data-field="endpoint" caption="Key" :allowHeaderFiltering="false" />
                <DxColumn data-field="is_active" caption="Status" cell-template="cctv-status" :allowFiltering="true" :allowHeaderFiltering="false" data-type="boolean" false-text="Inactive" true-text="Active"/>
                <template #cctv-status="{ data }">
                    <span v-if="data.data.is_active"
                        class="px-4 py-2 rounded-md bg-success text-white text-xs">Active</span>
                    <span v-else class="px-4 py-2 rounded-md bg-danger text-white text-xs">Inactive</span>
                </template>
                <DxColumn cell-template="action" width="60" alignment="center" :allowExporting="false" :showInColumnChooser="false" :fixed="true" fixed-position="right" v-if="can('cctv.update|cctv.delete')"/>
                <template #action="{ data }">
                    <el-dropdown trigger="click" placement="bottom-end">
                        <span class="el-dropdown-link">
                            <BsIcon icon="ellipsis-vertical" />
                        </span>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item v-if="can('cctv.update')" @click="editCctvAction(data.data)" >
                                    <BsIcon icon="pencil-square" class="mr-2" /> Edit cctv
                                </el-dropdown-item>
                                <el-dropdown-item v-if="!data.data.is_active && can('cctv.update')" @click="switchCctvStatus(data.data, true)">
                                    <BsIcon icon="arrow-path-rounded-square" class="mr-2" /> Enable cctv
                                </el-dropdown-item>
                                <el-dropdown-item v-else-if="can('cctv.update')" @click="switchCctvStatus(data.data, false)">
                                    <BsIcon icon="arrow-path-rounded-square" class="mr-2" /> Disable cctv
                                </el-dropdown-item>
                                <el-dropdown-item v-if="can('cctv.delete')" @click="deleteCctvAction(data.data)">
                                    <BsIcon icon="trash" class="mr-2" /> Delete cctv
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </template>

                <!-- Custom toolbar -->
                <DxToolbar>
                    <DxItem location="before" template="buttonTemplate" />
                    <DxItem name="columnChooserButton" />
                    <DxItem name="exportButton" />
                    <DxItem widget="dxButton" :options="{ icon: 'refresh', onClick: refreshDatagrid }" />
                </DxToolbar>
                <template #buttonTemplate>
                    <div class="flex w-full">
                        <Transition name="fadetransition" mode="out-in" appear>
                            <div v-if="!itemSelected">
                                <!-- Table Header Action Here -->
                            </div>
                            <div v-else class="flex items-center border-2 border-primary-border rounded-full gap-1 text-sm">
                                <BsIconButton icon="x-mark" @click="clearSelection" />
                                <span class="font-bold mr-2">{{ dataSelected.length }} Selected</span>

                                <div class="flex items-center border-l-2 px-2 h-full gap-1">
                                    <div class="flex items-center rounded-full hover:bg-gray-200 cursor-pointer" @click="switchCctvStatus(dataSelected, true)"  v-if="can('cctv.update')">
                                        <BsIconButton icon="check-circle" class="text-success" />
                                        <span class="mr-2 font-semibold">Enable</span>
                                    </div>
                                    <div class="flex items-center rounded-full hover:bg-gray-200 cursor-pointer" @click="switchCctvStatus(dataSelected, false)"  v-if="can('cctv.update')">
                                        <BsIconButton icon="x-circle" class="text-danger" />
                                        <span class="mr-2 font-semibold">Disable</span>
                                    </div>
                                    <p class="font-semibold italic text-gray-700" v-if="!can('cctv.update')">No Action</p>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </template>
            </DxDataGrid>
        </div>


        <!-- Modal -->
        <el-dialog v-model="dialogFormVisible" width="500px" :append-to-body="true" :destroy-on-close="true"
            class="!rounded-xl">
            <template #header>
                <span class="font-bold text-lg">{{ !editMode ? 'Create' : 'Edit' }} Cctv</span>
            </template>
            <el-form ref="formCctvRef" :model="formCctv" label-width="200px" label-position="top"
                require-asterisk-position="right" autocomplete="off">
                <el-form-item :error="getFormError('source_name')" prop="source_name" label="Source name" :required="true">
                    <el-input v-model="formCctv.source_name" autocomplete="one-time-code" autocorrect="off"
                        spellcheck="false" />
                </el-form-item>
                <el-form-item :error="getFormError('url_streaming')" prop="url_streaming" label="Url Stream" :required="true">
                    <el-input v-model="formCctv.url_streaming" autocomplete="one-time-code" autocorrect="off"
                        spellcheck="false" />
                </el-form-item>
                <el-form-item :error="getFormError('warehouse_id')" prop="ms_warehouse_id" label="Warehouse" :required="true">
                    <el-select v-model="formCctv.ms_warehouse_id" placeholder="Select warehouse">
                        <el-option v-for="warehouse in warehouses" :label="warehouse.warehouse_name" :value="warehouse.id" />
                    </el-select>
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer flex">
                    <BsButton class="flex-grow" type="primary-outline" @click="closeDialog">Cancel</BsButton>
                    <BsButton class="flex-grow" v-if="!editMode" type="primary" @click="addCctvSubmitAction">Submit</BsButton>
                    <BsButton class="flex-grow" v-if="editMode" type="primary" @click="editCctvSubmitAction">Update</BsButton>
                </span>
            </template> 
        </el-dialog>
    </MainLayout>

</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import BsButton from '@granule/Components/BsButton.vue';
import { ref, computed } from 'vue';
import { can } from '@granule/Core/Helpers/permission-check';
import CustomStore from "devextreme/data/custom_store";
import { dxLoad } from '@granule/Core/Helpers/dx-helpers';
import {
    DxColumn,
    DxDataGrid,
    DxItem,
    DxToolbar
} from 'devextreme-vue/data-grid';
import BsIcon from '@granule/Components/BsIcon.vue';
import BsIconButton from '@granule/Components/BsIconButton.vue';
import axios from 'axios';

const formCctvRef = ref();
const editMode = ref(false);
const dialogFormVisible = ref(false);
const formCctvErrors = ref([]);
const warehouses = computed(() => usePage().props.warehouse);
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;



const formCctv = useForm({
    cctv_id: '',
    ms_warehouse_id: '',
    source_name: '',
    url_streaming: '',
    endpoint: '',
});

function addCctvAction() {
    editMode.value = false;
    dialogFormVisible.value = true;

    formCctv.cctv_id = '';
    formCctv.ms_warehouse_id = '';
    formCctv.source_name = '';
    formCctv.url_streaming = '';
    formCctv.endpoint = '';
}

async function addCctvSubmitAction() {
    await formCctvRef.value.validate((valid, _) => {
        if (valid) {
            formCctv.post(route('cctv.create'), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    formCctvErrors.value = [];
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formCctvErrors.value = errors;
                    if('message' in errors){
                        ElMessage({
                            message: errors.message,
                            type: 'error',
                        });
                    }
                }
            });
        }
    });
}

function editCctvAction(dataCctv) {
    editMode.value = true;
    dialogFormVisible.value = true;   

    formCctv.cctv_id =dataCctv.id;
    formCctv.ms_warehouse_id =dataCctv.warehouse.id;
    formCctv.source_name = dataCctv.source_name;
    formCctv.url_streaming = dataCctv.url_streaming;
    formCctv.endpoint = dataCctv.endpoint;
}

async function editCctvSubmitAction() {
    await formCctvRef.value.validate(async (valid, _) => {
        if (valid) {
            formCctv.put(route('cctv.update',formCctv.cctv_id), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    formCctvErrors.value = [];
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formCctvErrors.value = errors;
                    if('message' in errors){
                        ElMessage({
                            message: errors.message,
                            type: 'error',
                        });
                    }
                }
            });
        }
    });
}

function deleteCctvAction(dataCctv) {
    ElMessageBox.confirm(
        'Are you sure to delete this cctv ?',
        'Warning',
        {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
        }
    )
        .then(() => {
            router.delete(route('cctv.delete',dataCctv.id), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formCctvErrors.value = errors;
                }
            });
        })
        .catch(() => {
            ElMessage({
                type: 'info',
                message: 'Action Canceled',
            })
        })
}

function switchCctvStatus(dataCctv, status) {
    if (Array.isArray(dataCctv)) {
        ElMessageBox.confirm(
            'Are you sure to switch these cctv status to ' + (status ? 'Active' : 'Inactive') + ' ?',
            'Warning',
            {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'warning',
            }
        ).then(() => {
            dataCctv.forEach((cctv) => {
                switchCctvStatus(cctv, status);
            });
        }).catch(() => {
            ElMessage({
                type: 'info',
                message: 'Action Canceled',
            })
        });
    } else {               
        axios.put(route('cctv.switch_status', dataCctv.id), {
            is_active: status,
            channel: dataCctv.source_name
        }).then((response) => {
            ElMessage({
                message: response.data.message,
                type: 'success',
            });
            refreshDatagrid();
        }).catch((error) => {
            ElMessage({
                message: error.response?.data?.error || 'Failed to switch status',
                type: 'error',
            });
        });
    }
}

function getFormError(field, errors = formCctvErrors.value) {
    if (!errors && !errors.length) {
        return false
    }
    if (errors[field]) {
        return errors[field]
    }
}

function closeDialog() {
    dialogFormVisible.value = false;
}


// ========================================================================
// Data source using server side processing
// ========================================================================
const dataKey = 'id'; //change to data primary key
const dataRoute = route('cctv.data_processing') //change to data processing route
const dataSource = new CustomStore({
    key: dataKey,
    load: dxLoad(dataRoute).bind(this),
}); 


// DEVEXTREME DATAGRID
const datagridRef = ref();
const dataSelected = ref([]);
var itemSelected = computed(() => dataSelected.value.length > 0);

// On Refresh Datagrid
function refreshDatagrid() {
    datagridRef.value.instance.refresh();
};

// On Selection Changed
function onSelectionChanged(data) {
    dataSelected.value = data.selectedRowsData;
};

// Clear Selection
function clearSelection() {
    const dataGrid = datagridRef.value.instance;
    dataGrid.clearSelection();
    dataSelected.value = [];
}

</script>
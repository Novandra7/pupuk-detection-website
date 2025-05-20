<template>
    <Head title="Bag Management"/>
    <MainLayout title="Bag Management">
        <template #header-action>
            <BsButton type="primary" icon="plus" @click="addBagAction" v-if="can('bag.create')">Add Bag</BsButton>
        </template>
        <div class="flex flex-row">
            <DxDataGrid ref="datagridRef" :data-source="dataSource" key="id" @selection-changed="onSelectionChanged" @cell-dbl-click="editBagAction($event.data)">
                <DxColumn data-field="bag_type" caption="Bag Type" :allowHeaderFiltering="false" />
                <DxColumn data-field="weight_in_kilogram" caption="Weight in Kilogram" :allowHeaderFiltering="false" alignment="left"/>
                <DxColumn cell-template="image" data-field="image" caption="Image" :allowHeaderFiltering="false" :allowFiltering="false"/>
                <template #image="{ data }">
                     <img :src="`/storage/${data.value}`" alt="Image not found" style="height: 60px; object-fit: cover;" />
                </template>
                <DxColumn data-field="is_active" caption="Status" cell-template="bag-status" :allowFiltering="true" :allowHeaderFiltering="false" data-type="boolean" false-text="Inactive" true-text="Active"/>
                <template #bag-status="{ data }">
                    <span v-if="data.data.is_active"
                        class="px-4 py-2 rounded-md bg-success text-white text-xs">Active</span>
                    <span v-else class="px-4 py-2 rounded-md bg-danger text-white text-xs">Inactive</span>
                </template>
                <DxColumn cell-template="action" width="60" alignment="center" :allowExporting="false" :showInColumnChooser="false" :fixed="true" fixed-position="right" v-if="can('bag.update|bag.delete')"/>
                <template #action="{ data }">
                    <el-dropdown trigger="click" placement="bottom-end">
                        <span class="el-dropdown-link">
                            <BsIcon icon="ellipsis-vertical" />
                        </span>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item v-if="can('bag.update')" @click="editBagAction(data.data)" >
                                    <BsIcon icon="pencil-square" class="mr-2" /> Edit bag
                                </el-dropdown-item>
                                <el-dropdown-item v-if="!data.data.is_active && can('bag.update')" @click="switchBagStatus(data.data, true)">
                                    <BsIcon icon="arrow-path-rounded-square" class="mr-2" /> Enable bag
                                </el-dropdown-item>
                                <el-dropdown-item v-else-if="can('bag.update')" @click="switchBagStatus(data.data, false)">
                                    <BsIcon icon="arrow-path-rounded-square" class="mr-2" /> Disable bag
                                </el-dropdown-item>
                                <el-dropdown-item v-if="can('bag.delete')" @click="deleteBagAction(data.data)">
                                    <BsIcon icon="trash" class="mr-2" /> Delete bag
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
                                    <div class="flex items-center rounded-full hover:bg-gray-200 cursor-pointer" @click="switchBagStatus(dataSelected, true)"  v-if="can('bag.update')">
                                        <BsIconButton icon="check-circle" class="text-success" />
                                        <span class="mr-2 font-semibold">Enable</span>
                                    </div>
                                    <div class="flex items-center rounded-full hover:bg-gray-200 cursor-pointer" @click="switchBagStatus(dataSelected, false)"  v-if="can('bag.update')">
                                        <BsIconButton icon="x-circle" class="text-danger" />
                                        <span class="mr-2 font-semibold">Disable</span>
                                    </div>
                                    <p class="font-semibold italic text-gray-700" v-if="!can('bag.update')">No Action</p>
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
                <span class="font-bold text-lg">{{ !editMode ? 'Create' : 'Edit' }} Bag</span>
            </template>
            <el-form ref="formBagRef" :model="formBag" label-width="200px" label-position="top"
                require-asterisk-position="right" autocomplete="off">
                <el-form-item :error="getFormError('bag_type')" prop="bag_type" label="Bag type" :required="true">
                    <el-input v-model="formBag.bag_type" autocomplete="one-time-code" autocorrect="off"
                        spellcheck="false" />
                </el-form-item>
                <el-form-item :error="getFormError('weight_in_kilogram')" prop="weight_in_kilogram" label="Weight in kilogram" :required="true">
                    <el-input v-model="formBag.weight_in_kilogram" autocomplete="one-time-code" autocorrect="off"
                        spellcheck="false" />
                </el-form-item>
                <el-form-item :error="getFormError('image')" label="Bag Image" prop="image">
                    <el-upload :on-change="handleFileChange" :on-remove="handleFileRemove" drag class="!w-full" :auto-upload="false" :limit="1" accept="image/jpeg,image/png">
                        <div class="flex justify-center items-center ">
                            <BsIcon v-if="!previewImageUrl" icon="cloud-arrow-up" class=""/><upload-filled />
                            <img v-if="previewImageUrl" :src="previewImageUrl" alt="Preview" class="h-full object-cover rounded-md" />
                        </div>
                        <div v-if="!previewImageUrl" class="el-upload__text">
                            Drop file here or <em>click to upload</em>
                        </div>
                        <template #tip>
                            <div class="el-upload__tip">
                                jpg/png files with a size up to 2MB
                            </div>
                        </template>
                    </el-upload>
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer flex">
                    <BsButton class="flex-grow" type="primary-outline" @click="closeDialog">Cancel</BsButton>
                    <BsButton class="flex-grow" v-if="!editMode" type="primary" @click="addBagSubmitAction">Submit</BsButton>
                    <BsButton class="flex-grow" v-if="editMode" type="primary" @click="editBagSubmitAction">Update</BsButton>
                </span>
            </template> 
        </el-dialog>
    </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
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


const formBagRef = ref();
const editMode = ref(false);
const dialogFormVisible = ref(false);
const formBagErrors = ref([]);
const previewImageUrl = ref('');

const formBag = useForm({
    bag_id: '',
    bag_type: '',
    weight_in_kilogram: '',
    image: null,
});

function handleFileChange(file) {
    if (file && file.raw) {
        formBag.image = file.raw;
        previewImageUrl.value = URL.createObjectURL(file.raw);
    }
}

function handleFileRemove(file) {   
    if (file && file.raw) {
        formBag.image = '';
        previewImageUrl.value = '';
    }
}


function addBagAction() {
    editMode.value = false;
    dialogFormVisible.value = true;

    formBag.bag_id = '';
    formBag.bag_type = '';
    formBag.weight_in_kilogram = '';
    formBag.image = [];
    previewImageUrl.value = ''    
}

async function addBagSubmitAction() {
    await formBagRef.value.validate((valid, _) => {
        if (valid) {
            formBag.post(route('bag.create'), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    formBagErrors.value = [];
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formBagErrors.value = errors;
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

function editBagAction(dataBag) {
    editMode.value = true;
    dialogFormVisible.value = true;
    
    formBag.bag_id =dataBag.id;
    formBag.bag_type =dataBag.bag_type;
    formBag.weight_in_kilogram =dataBag.weight_in_kilogram;
    formBag.image =dataBag.image;
    previewImageUrl.value = `/storage/${dataBag.image}`;        
}

async function editBagSubmitAction() {    
    await formBagRef.value.validate(async (valid, _) => {
        if (valid) {                                  
            formBag.post(route('bag.update',formBag.bag_id), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    formBagErrors.value = [];
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {   
                    console.log('gagal cik');        
                    formBagErrors.value = errors;
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

function deleteBagAction(dataBag) {
    ElMessageBox.confirm(
        'Are you sure to delete this bag ?',
        'Warning',
        {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
        }
    )
        .then(() => {
            router.delete(route('bag.delete',dataBag.id), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formBagErrors.value = errors;
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

function switchBagStatus(dataBag, status) {
    if (Array.isArray(dataBag)) {
        ElMessageBox.confirm(
            'Are you sure to switch these bags status to ' + (status ? 'Active' : 'Inactive') + ' ?',
            'Warning',
            {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'warning',
            }
        ).then(() => {
            dataBag.forEach((bag) => {
                switchBagStatus(bag, status);
            });
        }).catch(() => {
            ElMessage({
                type: 'info',
                message: 'Action Canceled',
            })
        });
    } else {
        axios.put(route('bag.switch_status', dataBag.id), {
            is_active: status
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

function getFormError(field, errors = formBagErrors.value) {
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
const dataRoute = route('bag.data_processing') //change to data processing route
const dataSource = new CustomStore({
    key: dataKey,
    async load(loadOptions) {
        const result = await dxLoad(dataRoute).call(this, loadOptions);
        if (result && result.data && result.data.length > 0) {
            // Skip baris pertama
            result.data = result.data.slice(2);
            // Update totalCount kalau pakai pagination
            if (result.totalCount !== undefined) {
                result.totalCount = result.totalCount - 1;
            }
        }
        return result;
    }
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
<script setup lang="ts">
import { WebReference, Reference, ReferenceType } from '@/lib/references';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '../PrimaryButton.vue';
import { Loader2Icon } from "lucide-vue-next";
import axios from 'axios';
import { computed, ref } from 'vue';
import { DatePickerClose, Separator } from 'reka-ui';
import { DocumentData } from '@/types';
import { Search } from 'lucide-vue-next';
import { CalendarDate, getLocalTimeZone, parseAbsolute, parseAbsoluteToLocal, parseDate, parseDateTime, today } from '@internationalized/date';
import {
  DateFieldInput,
  DateFieldRoot,
} from 'reka-ui'

const props = defineProps<{
    document: DocumentData
}>();

const emit = defineEmits({
    submit: (id: string, ref: Reference) => {}
});

enum State {
    INITIAL,
    LOADING,
    FORM
};

interface FormField {
    label: string;
    id: string;
    type: 'text' | 'date' | 'array';
    required?: boolean;
    colspan?: 1 | 2;
};


const websiteFields:FormField[] = [
    { id: 'author', label: 'Autor', type: 'text', required: true },
    { id: 'title', label: 'Título', type: 'text', required: true },
    { id: 'url', label: 'Url', type: 'text', required: true },
    { id: 'date', label: 'Data de Publicação', type: 'date', colspan: 1, required: true },
    { id: 'visited', label: 'Data de Acesso', type: 'date', colspan: 1, required: true },
];


type FieldObject = {
    [key: string]: string | CalendarDate;
};

const fields = ref<FieldObject>({});
const errors = ref<FieldObject>({});

const state = ref<State>(State.INITIAL);

async function search(url: string) {
    try {
        const promise = axios.get(route('references.from_website', { url }));
        state.value = State.LOADING;

        const { data } = await promise;

        const entries = websiteFields.map(({ id, type }) => {
            switch (type) {
                case 'date':
                    let date = null;

                    if (Object.hasOwn(data, id)) {
                        date = parseDate(data[id] as string);
                    }

                    return [id, date]
                default:
                    return [id, Object.hasOwn(data, id) ? data[id] : ''];
            }
        });

        fields.value = Object.fromEntries(entries);
        state.value = State.FORM;
    } catch(e) {
        console.error(e);
        state.value = State.INITIAL;
    }
}

async function save(f: FieldObject) {
    const json = {
        type: ReferenceType.Web,
        reference: {}
    };

    const entries = Object.entries(f).map(([key, value]) => [key, value.toString()])
    json.reference = Object.fromEntries(entries);

    const _route = route('document.references', { id: props.document.id });

    const response = await axios.post(_route, json);
    const data : {
        id: string;
        reference: WebReference;
    }  = response.data;

    emit('submit', data.id, data.reference);
}

let query = '';
const isLoading = computed(() => state.value == State.LOADING);

</script>

<template>
    <div>
        <template v-if="state == State.INITIAL || isLoading" class="w-full">
            <InputLabel value="Adicionar Por Link" />
            <div class="w-full flex gap-4">
                <TextInput placeholder="Digite um endereço web para citar" v-model="query" class="grow"/>
                <PrimaryButton

                    @click.prevent="search(query)"
                    :disabled="isLoading"
                >
                    <Loader2Icon
                        v-if="isLoading"
                        role="status"
                        aria-label="Loading"
                        class="size-4 mr-2 text-neutral-100 opacity-80 animate-spin"
                    />
                    <Search
                        v-else
                        class="size-4 mr-2 text-neutral-100 opacity-80"
                    />
                    <span>Pesquisar</span>
                </PrimaryButton>
            </div>
            <p class="dark:text-neutral-200 mt-2 opacity-80 text-sm"> Inserimos automaticamente os detalhes encontrados para você </p>
            <div class="flex items-center my-8">
                <Separator class="bg-neutral-600 h-[1px] grow" />
                <p class="dark:text-neutral-200 opacity-80 text-sm px-2"> Ou </p>
                <Separator class="bg-neutral-600 h-[1px] grow" />
            </div>
                <p
                    class="text-center dark:text-neutral-200 text-md font-bold opacity-80 text-sm cursor-pointer"
                >
                    <a @click="state = State.FORM"> Ou Digite Manualmente -> </a>
                </p>
        </template>
        <template v-else-if="state == State.FORM">
            <form class="w-full grid grid-cols-2 gap-4">
                <div v-for="field in websiteFields" :class="field.colspan == 1 ? 'col-span-1' : 'col-span-2' ">
                    <InputLabel :value="field.label" />
                    <DateFieldRoot
                        v-if="field.type == 'date'"
                        v-model="fields[field.id] as CalendarDate"
                        locale="pt-BR"
                        v-slot="{ segments }"
                        granularity="day"
                        :is-date-unavailable="date => date.day === 19"
                        class="w-full flex select-none bg-neutral-900 text-neutral-200 border-neutral-700 text-md items-center rounded-lg shadow-sm text-center text-green10 border data-[invalid]:border-red-500"
                    >
                      <template
                        v-for="item in segments"
                        :key="item.part"
                      >
                        <DateFieldInput
                          v-if="item.part === 'literal'"
                          :part="item.part"
                        class=""
                        >
                          {{ item.value }}
                        </DateFieldInput>
                        <DateFieldInput
                          v-else
                          :part="item.part"
                          class="rounded py-1 px-2 focus:outline-none focus:border-1 focus:border-indigo-500 data-[placeholder]:text-green9"
                        >
                          {{ item.value }}
                        </DateFieldInput>
                      </template>
                    </DateFieldRoot>
                    <TextInput v-else v-model="fields[field.id] as string" class="w-full" />
                </div>
                <div class="w-full col-span-2 flex justify-end mt-4 gap-4 pr-2">
                    <PrimaryButton @click.prevent="fields = {}; state = State.INITIAL">Voltar</PrimaryButton>
                    <PrimaryButton @click.prevent="save(fields)">Salvar</PrimaryButton>
                </div>
            </form>
        </template>
    </div>
</template>

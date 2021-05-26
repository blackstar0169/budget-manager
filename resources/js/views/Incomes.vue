<template>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Label</th>
                <th>Amount</th>
                <th>Start date</th>
                <th>Reucurrence</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="income in incomes">
                <td>
                    <input type="text" @change="updateIncome(income)" v-model="income.label">
                </td>
                <td class="amount" :class="{ 'table-danger': income.amount < 0, 'table-success': income.amount >= 0 }">
                    <input type="number" @change="updateIncome(income)" v-model="income.amount">
                </td>
                <td class="date">
                    <input type="date" @change="updateIncome(income)" v-model="income.date">
                </td>
                <td>
                    <span v-if="income.recurrence !== 'unique'">Each</span>
                    <input v-if="income.recurrence !== 'unique'" @change="updateIncome(income)" v-model="income.recurrenceNumber" type="number">
                    <select type="text" v-model="income.recurrence" @change="updateIncome(income)">
                        <option v-for="(label, index) in recurrences" :value="index">{{label}}</option>
                    </select>
                </td>
                <td><button @click="deleteIncome(income)">Delete</button></td>
            </tr>
            <tr class="new-income">
                <td>
                    <input type="text" v-model="newIncome.label">
                </td>
                <td class="amount">
                    <input type="number" min="1" class="amount" v-model="newIncome.amount">
                </td>
                <td>
                    <input type="date" class="date" v-model="newIncome.date">
                </td>
                <td>
                    <span v-if="newIncome.recurrence !== '' && newIncome.recurrence !== 'unique'">Each</span>
                    <input v-if="newIncome.recurrence !== '' && newIncome.recurrence !== 'unique'" v-model="newIncome.recurrenceNumber" type="number">
                    <select type="text" v-model="newIncome.recurrence">
                        <option v-for="(label, index) in recurrences" :value="index">{{label}}</option>
                    </select>
                </td>
                <td><button @click="addIncome">Validate</button></td>
            </tr>
        </tbody>
    </table>

</template>

<script>
import axios from "axios";
import {hash, only} from "../helpers";

export default {
    name: 'Incomes',
    computed: {},
    mounted() {
        this.loadIncomes();
    },
    methods: {
        loadIncomes() {
            axios.get('/api/incomes').then(res => {
                this.incomes = res.data;
                for (let i = 0; i < this.incomes.length; i++) {
                    this.fillIncome(this.incomes[i]);
                }
            });
        },
        deleteIncome(income) {
            axios.delete('/api/incomes/' + income.id).then(res => {
                if (res.data.status === true) {
                    this.incomes = this.incomes.filter(item => item.id !== income.id);
                }
            });
        },
        updateIncome(income) {
            if(income.checksum !== this.getIncumChecksum(income)) {
                axios.patch('/api/incomes/'+income.id, income).then(res => {
                    if (res.data.status === true) {
                        for (let i = 0; i < this.incomes.length; i++) {
                            if (this.incomes[i].id === income.id) {
                                this.incomes[i] = this.fillIncome(income);
                            }
                        }
                    }
                });
            }
        },
        addIncome() {
            axios.post('/api/incomes', this.newIncome).then(res => {
                if (res.data.status === true) {
                    this.incomes.push(this.fillIncome(res.data.income));
                }

                this.newIncome = {
                    label: '',
                    amount: '',
                    date: '',
                    recurrenceNumber: 0,
                    recurrence: 'unique'
                }
            });
        },
        fillIncome(income) {
            if (typeof income.recurrenceNumber !== 'number') {
                income.recurrenceNumber = parseInt(income.recurrenceNumber, 10);
            }
            income.checksum = this.getIncumChecksum(income);

            return income;
        },
        getIncumChecksum(income) {
            let str = only(income, ['label', 'amount', 'date', 'recurrenceNumber', 'recurrence'], false).join('-');
            return hash(str);
        }
    },
    data() {
        return {
            incomes: [],
            recurrences: {
                unique: 'Unique',
                year: 'Year',
                month: 'Month',
                week: 'Week',
                day: 'Day'
            },
            newIncome: {
                label: '',
                amount: '',
                date: '',
                recurrenceNumber: 0,
                recurrence: 'unique'
            }
        }
    }
};
</script>

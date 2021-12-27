<template>
    <div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Expected</th>
                    <th>Difference</th>
                    <th>Benefice</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="future in futures">
                    <td>{{future.date}}</td>
                    <td v-if="!future.amountRequired" class="text-right amount">
                        <span class="far fa-edit">{{future.amount}} €</span>
                        <button class="btn-icon" title="Edit" v-if="future.isEditable" @click="future.amountRequired = true">
                            <span class="icofont-ui-edit"></span>
                        </button>
                    </td>
                    <td v-if="future.amountRequired" class="text-right amount">
                        <div class="input-group">
                            <input class="form-control" v-model="future.amount" type="number">
                            <div class="input-group-append">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                        <button class="hover-icon btn-icon" title="Validate" @click="updatePrevision(future)">
                            <span class="icofont-check"></span>
                        </button>
                    </td>
                    <td>
                        {{future.expected}} €
                        <span class="badge bg-secondary" data-bs-toggle="modal" data-bs-target="#incomes-modal" @click="updateIncomesModal(future)">i</span>
                    </td>
                    <td :class="{ 'table-danger': future.diff < 0, 'table-success': future.diff >= 0 }">{{future.diff}} €</td>
                    <td :class="{ 'table-danger': future.benef < 0, 'table-success': future.benef >= 0 }">{{future.benef}} €</td>
                </tr>
            </tbody>
        </table>

        <!-- Modal -->
        <div class="modal fade" id="incomes-modal" tabindex="-1" aria-labelledby="incomes-modal-label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="incomes-modal-label">Incomes/Outcomes list</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped" v-if="typeof modalFuture === 'object' && modalFuture !== null">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="income in modalFuture.incomes">
                                <td>{{income.label}}</td>
                                <td :class="{ 'table-danger': income.amount < 0, 'table-success': income.amount >= 0 }">{{income.amount}}</td>

                            </tr>
                        </tbody>
                    </table>
                    <div>Total : {{modalFutureSum}}€</div>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
table {
    vertical-align: middle;
}
table tbody tr td.amount{
    display: flex;
    align-items: center;
}
table tbody tr td.amount .btn{
    visibility: hidden;
}
table tbody tr:hover td.amount .btn{
    visibility: visible;
}
</style>

<script>
import axios from 'axios';
import moment from 'moment';
import {round} from '../helpers.js';
require('moment-recur');


export default {
    name: 'Future',
    computed: {},
    mounted() {
        this.loadDatas();
    },
    methods: {
        loadDatas() {
            axios.get('/api/incomes').then(res => {
                this.incomes = res.data.map((item) => {
                    item.amount = parseFloat(item.amount);
                    item.id = parseInt(item.id);
                    item.recurrenceNumber = parseInt(item.recurrenceNumber);
                    return item;
                });
                if (this.situations !== null) {
                    this.renderFutures();
                }
            });
            axios.get('/api/situations').then(res => {
                this.situations = res.data.map((item) => {
                    item.amount = parseFloat(item.amount);
                    item.expected = parseFloat(item.expected);
                    return item;
                }).sort((a, b) => {
                    return moment(a.date).isAfter(b.date)
                })

                if (this.incomes !== null) {
                    this.renderFutures();
                }
            });
        },
        renderFutures() {
            var futures = [];
            var hasAmountRequired = false;
            var start = moment(window.config.start_date, 'MMYYYY');
            start.startOf('month');
            var end = moment();
            var prevAmount = round(parseFloat(window.config.start_money), 2);
            end.add(2, 'years').endOf('month');

            // Add start config
            futures.push({
                date: start.format('MMM YYYY'),
                amount: prevAmount,
                incomes: [],
                expected: '-',
                diff: '-',
                benef: '-'
            });

            // Manualy registred predictions
            for (let i = 0; i < this.situations.length; i++) {
                var date = moment(this.situations[i].date);
                var amount = round(parseFloat(this.situations[i].amount), 2);
                start = date;
                futures.push({
                    id: this.situations[i].id,
                    date: date.format('MMM YYYY'),
                    amount: amount,
                    isEditable: false,
                    incomes: null,
                    amountRequired: false,
                    expected: round(parseFloat(this.situations[i].expected), 2),
                    diff: round(amount - this.situations[i].expected, 2),
                    benef: prevAmount !== null ? round(amount - prevAmount, 2) : 0
                });
                prevAmount = amount;
            }

            // Start forcasting from the next month
            start.add(1, 'month');
            while(start.isBefore(end)) {
                var expected = this.getExpectation(prevAmount, start);
                let endCurrentMonth = moment(start).endOf('month');
                let amountRequired = (start.isBefore(moment().startOf('month'), 'day') || moment().isSameOrAfter(endCurrentMonth, 'day')) && !hasAmountRequired;
                hasAmountRequired = amountRequired|hasAmountRequired;
                futures.push({
                    date: start.format('MMM YYYY'),
                    amount: null,
                    isEditable: true,
                    amountRequired: amountRequired,
                    expected: expected.expected,
                    incomes: expected.incomes,
                    diff: ' - ',
                    benef: round(expected.expected - prevAmount, 2)
                });
                prevAmount = expected.expected;
                start.add(1, 'month');
            }

            this.futures = futures;
        },
        getExpectation(prevAmount, start) {
            var ret = {
                expected: round(prevAmount, 2),
                incomes: []
            };
            for (let i = 0; i < this.incomes.length; i++) {
                const income = this.incomes[i];
                let current = moment(start);
                let end = moment(start);
                end.endOf('month');
                let date = moment(income.date);

                if (income.recurrence !== 'unique') {
                    let recurrence = date.recur().every(parseInt(income.recurrenceNumber), income.recurrence);
                    let match = false;

                    while (current.isBefore(end) && !match) {
                        if (recurrence.matches(current)) {
                            match = true;
                            // console.log(income.amount);
                            ret.expected += income.amount;
                            ret.incomes.push(income);
                        }
                        current.add(1, 'day');
                    }
                } else if (income.recurrence === 'unique' && date.isBetween(start, end)) {
                    // console.log(income.amount);
                    ret.expected += income.amount;
                    ret.incomes.push(income);
                }
            }

            // console.log(ret.expected);

            ret.expected = round(ret.expected, 2);
            return ret;
        },
        updatePrevision(future) {
            let value = round(parseFloat(future.amount), 2);
            if (typeof future.id !== 'undefined') {
                axios.put('/api/situations/'+future.id, {
                    amount: value
                }).then(res => {
                    future.amountRequired = false;
                    if (res.data.status === true) {
                        for (let i = 0; i < this.situations.length; i++) {
                            if (this.situations[i].id === future.id) {
                                this.situations[i] = res.data.situation;
                            }
                        }
                    }
                    this.renderFutures();
                });
            } else {
                console.log(future);
                axios.post('/api/situations', {
                    date: moment(future.date, 'MMM YYYY').startOf('month').format('YYYY-MM-DD'),
                    amount: value,
                    expected: future.expected
                }).then(res => {
                    if (res.data.status === true) {
                        future.id = res.data.situation.id;
                        future.amountRequired = false;
                        this.situations.push(res.data.situation);
                        this.situations.sort((a, b) => {
                            return moment(a.date).isAfter(b.date);
                        });

                        this.renderFutures();
                    }
                });
            }
        },
        updateIncomesModal(future) {
            console.log(future);
            this.modalFuture = future;
            this.modalFutureSum = 0;
            for (let i = 0; i < future.incomes.length; i++) {
                this.modalFutureSum += future.incomes[i].amount;

            }
            this.modalFutureSum = round(parseFloat(this.modalFutureSum), 2);
        }
    },
    data() {
        return {
            incomes:null,
            situations:null,
            futures: [],
            modalFuture: null,
            modalFutureSum: 0,
        }
    }
};
</script>

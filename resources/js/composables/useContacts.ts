import { ref, reactive } from 'vue';
import {
    createContact,
    deleteContactById,
    deleteMultipleContacts,
    getContacts,
    mapContactsForTable,
    updateContactById,
} from '@/services/contact.service';
import { Contact, ContactError, ContactTableFormat } from '@/types';

export function useContacts(toast: any, initContactItems: Contact[] = []) {
    const contacts = ref<ContactTableFormat[]>(mapContactsForTable(initContactItems));
    const contactFormErrors = reactive<Partial<ContactError>>({});

    const pageNumber = ref(1);
    const pageCount = ref(0);
    const limit = ref(5);
    const contactCount = ref(0);
    const tableStartingIndex = ref<number>(1);

    const fetchContacts = async () => {
        const [data, error] = await getContacts();

        if (error) toast.value?.showToast(error.error, 'error');

        if (data) {
            contacts.value = mapContactsForTable(data.contacts);
            pageNumber.value = data.pageNumber;
            pageCount.value = data.pageCount;
            limit.value = data.limit;
            contactCount.value = data.contactCount;
        }
    };

    const submitContact = async (form: Partial<Contact>) => {
        const isNew = !form.id;
        const apiCall = isNew ? createContact : updateContactById.bind(null, form.id!);

        const formData = new FormData();

        Object.entries(form).forEach(([key, value]) => {
            if (key !== 'id' && value !== undefined) formData.append(key, String(value));
        });

        const [data, err] = await apiCall(formData);

        if (err) {
            if ('error' in err) 
                toast.value?.showToast(err.error, 'error');
            else {
                Object.keys(contactFormErrors).forEach(k =>
                    contactFormErrors[k as keyof ContactError] = undefined
                );

                Object.keys(err).forEach(k =>
                    contactFormErrors[k as keyof ContactError] = err[k as keyof ContactError][0]
                );
            }
            return null;
        }

        toast.value?.showToast(data!.message, 'success');
        await fetchContacts();

        return data;
    };

    const deleteContact = async (id: number) => {
        const [data, err] = await deleteContactById(id);
        if (err) {
            toast.value?.showToast(err.error, 'error');
            return null;
        }

        toast.value?.showToast(data!.message ?? 'Deleted successfully.', 'success');

        if (contacts.value.length === 1 && pageNumber.value > 1) {
            pageNumber.value -= 1;
            const url = new URL(window.location.href);
            url.searchParams.set("page", String(pageNumber.value));
            window.history.pushState({}, '', url);
        }

        await fetchContacts();
        return data;
    };

    const deleteSelectedContacts = async (ids: number[]) => {
        const res = await deleteMultipleContacts(ids);
        toast.value?.showToast(res.message);

        if (contacts.value.length === ids.length && pageNumber.value > 1) {
            pageNumber.value -= 1;

            const url = new URL(window.location.href);
            url.searchParams.set("page", String(pageNumber.value));
            window.history.pushState({}, '', url);
        }

        await fetchContacts();
    };

    const setPaginationStates = (state: {
        limit: number;
        pageNumber: number;
        pageCount: number;
        contactCount: number;
    }) => {
        pageNumber.value = state.pageNumber;
        pageCount.value = state.pageCount;
        limit.value = state.limit;
        tableStartingIndex.value = (state.pageNumber - 1) * state.limit;
        contactCount.value = state.contactCount;
    }

    const clearErrors = () => Object.keys(contactFormErrors).forEach(key => {
        contactFormErrors[key as keyof ContactError] = undefined;
    });

    return {
        contacts,
        contactFormErrors,

        fetchContacts,
        deleteContact,
        deleteSelectedContacts,
        submitContact,
        setPaginationStates,
        clearErrors,

        pageNumber,
        pageCount,
        limit,
        contactCount,
        tableStartingIndex
    };
}

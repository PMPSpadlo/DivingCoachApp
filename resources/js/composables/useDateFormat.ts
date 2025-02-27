export function useDateFormat() {
    const formatDate = (dateString: string): string => {
        if (!dateString) return '';

        const date = new Date(dateString);
        return date.toLocaleDateString('pl-PL');
    };

    const formatDateTime = (dateString: string): string => {
        if (!dateString) return '';

        const date = new Date(dateString);
        return date.toLocaleDateString('pl-PL') + ' ' + date.toLocaleTimeString('pl-PL', {
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    return {
        formatDate,
        formatDateTime
    };
}

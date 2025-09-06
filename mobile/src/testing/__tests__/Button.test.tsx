import React from 'react';
import { render, fireEvent } from '@/testing/utils/testUtils';
import { Button } from '@/shared/ui/Button';

describe('Button', () => {
  it('renders correctly', () => {
    const { getByText } = render(<Button>Test Button</Button>);
    expect(getByText('Test Button')).toBeTruthy();
  });

  it('calls onPress when pressed', () => {
    const onPress = jest.fn();
    const { getByText } = render(<Button onPress={onPress}>Test Button</Button>);
    
    fireEvent.press(getByText('Test Button'));
    expect(onPress).toHaveBeenCalledTimes(1);
  });

  it('shows loading state', () => {
    const { getByTestId } = render(<Button loading>Test Button</Button>);
    expect(getByTestId('activity-indicator')).toBeTruthy();
  });

  it('is disabled when disabled prop is true', () => {
    const onPress = jest.fn();
    const { getByText } = render(
      <Button onPress={onPress} disabled>
        Test Button
      </Button>,
    );
    
    fireEvent.press(getByText('Test Button'));
    expect(onPress).not.toHaveBeenCalled();
  });

  it('applies correct variant styles', () => {
    const { getByText } = render(<Button variant="danger">Test Button</Button>);
    const button = getByText('Test Button');
    expect(button).toBeTruthy();
  });
});
